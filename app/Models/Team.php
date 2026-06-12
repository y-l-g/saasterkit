<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\GeneratesUniqueTeamSlugs;
use App\Models\Concerns\HasPlanFeatures;
use App\Observers\TeamObserver;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Billable;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property-read Subscription|null $defaultSubscription
 * @property-read User $owner
 * @property-read Collection<int, TeamOwnershipInvitation> $ownershipInvitations
 * @property-read int|null $ownership_invitations_count
 * @property-read Collection<int, Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection<int, TeamInvitation> $teamInvitations
 * @property-read int|null $team_invitations_count
 * @property-read TeamUser|null $pivot
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\TeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team hasExpiredGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team onGenericTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team wherePmLastFour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team wherePmType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereStripeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy([TeamObserver::class])]
#[Fillable([
    'name',
    'slug',
])]
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use Billable, GeneratesUniqueTeamSlugs, HasFactory, HasPlanFeatures;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Team $team): void {
            if (blank($team->slug)) {
                $team->slug = static::generateUniqueTeamSlug($team->name);
            }
        });

        static::updating(function (Team $team): void {
            if ($team->isDirty('name')) {
                $team->slug = static::generateUniqueTeamSlug($team->name, $team->id);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsToMany<User, $this, Pivot>
     */
    public function users(): BelongsToMany
    {
        // @phpstan-ignore-next-line
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps()
            ->using(TeamUser::class);
    }

    /**
     * @return HasMany<TeamInvitation, $this>
     */
    public function teamInvitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    /**
     * @return HasMany<TeamOwnershipInvitation, $this>
     */
    public function ownershipInvitations(): HasMany
    {
        return $this->hasMany(TeamOwnershipInvitation::class);
    }

    public function hasUser(User $user): bool
    {
        return $this->users->contains($user) || $user->is($this->owner);
    }

    public function hasUserWithEmail(string $email): bool
    {
        return $this->users->contains(fn (User $user): bool => $user->email === $email);
    }

    public function removeUser(User $user): void
    {
        $user->forceFill(['current_team_id' => null])->save();

        $this->users()->detach($user);
    }

    /**
     * @return HasOne<Subscription, $this>
     */
    public function defaultSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('type', 'default');
    }

    public function purge(): void
    {
        DB::transaction(function (): void {
            User::query()->where('current_team_id', $this->id)
                ->update(['current_team_id' => null]);
            $this->users()->detach();
            $this->delete();
        });
    }
}
