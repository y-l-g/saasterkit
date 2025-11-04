<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasPlanFeatures;
use App\Observers\TeamObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Billable;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property-read \App\Models\Subscription|null $defaultSubscription
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TeamOwnershipInvitation> $ownershipInvitations
 * @property-read int|null $ownership_invitations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TeamInvitation> $teamInvitations
 * @property-read int|null $team_invitations_count
 * @property-read \App\Models\TeamUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereTrialEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUserId($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy([TeamObserver::class])]
class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use Billable, HasFactory, HasPlanFeatures;

    protected $fillable = [
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\User, $this, \Illuminate\Database\Eloquent\Relations\Pivot>
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TeamInvitation, $this>
     */
    public function teamInvitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TeamOwnershipInvitation, $this>
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
