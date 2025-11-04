<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\Teams\RoleEnum;
use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

/** @mixin User */
trait HasTeams
{
    /**
     * @return BelongsTo<Team, $this>
     */
    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function switchToTeam(Team $team): bool
    {
        if (! $this->belongsToTeam($team)) {
            return false;
        }
        $this->forceFill(['current_team_id' => $team->id])->save();
        $this->setRelation('currentTeam', $team);

        return true;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Team, $this>
     */
    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Team, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function teams(): BelongsToMany
    {
        // @phpstan-ignore-next-line
        return $this->belongsToMany(Team::class)
            ->withPivot('role')
            ->using(TeamUser::class)
            ->withTimestamps();
    }

    public function ownsTeam(Team $team): bool
    {
        return $this->id === $team->user_id;
    }

    public function belongsToTeam(Team|int $team): bool
    {
        $teamId = is_int($team) ? $team : $team->getKey();

        return $this->teams()->whereKey($teamId)->exists();
    }

    public function teamRole(Team $team): ?RoleEnum
    {
        $pivot = $team->users()->whereKey($this->getKey())->first()?->pivot;

        return $pivot?->role;
    }

    public function hasTeamPermission(Team $team, TeamMemberPermissionEnum $permission): bool
    {
        if ($this->ownsTeam($team)) {
            return true;
        }
        $role = $this->teamRole($team);
        if (! $role) {
            return false;
        }

        return in_array($permission, $role->permissions(), true);
    }

    /**
     * @return string[]
     */
    public function getPermissionsForTeam(Team $team): array
    {
        $cacheKey = "user.{$this->id}.team.{$team->id}.permissions";

        return Cache::remember($cacheKey, \Illuminate\Support\Facades\Date::now()->addHours(24), function () use ($team) {
            if (! $this->belongsToTeam($team)) {
                return [];
            }
            if ($this->ownsTeam($team)) {
                return collect(TeamMemberPermissionEnum::cases())->pluck('value')
                    ->values()
                    ->all();
            }

            return collect(TeamMemberPermissionEnum::cases())
                ->filter(fn (TeamMemberPermissionEnum $permission) => Gate::forUser($this)->check($permission->value, $team))
                ->pluck('value')
                ->values()
                ->all();
        });
    }
}
