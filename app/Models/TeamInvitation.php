<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Teams\RoleEnum;
use Database\Factories\TeamInvitationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team_id
 * @property string $email
 * @property RoleEnum|null $role
 * @property Carbon|null $accepted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Team $team
 *
 * @method static \Database\Factories\TeamInvitationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'accepted_at',
    'email',
    'role',
])]
class TeamInvitation extends Model
{
    public const int DEFAULT_EXPIRATION_DAYS = 7;

    /** @use HasFactory<TeamInvitationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'role' => RoleEnum::class,
        ];
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public static function defaultExpiresAt(): Carbon
    {
        return now()->addDays(self::DEFAULT_EXPIRATION_DAYS);
    }

    /**
     * @param  Builder<TeamInvitation>  $query
     * @return Builder<TeamInvitation>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('accepted_at');
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function isPending(): bool
    {
        return ! $this->isAccepted();
    }

    public function markAccepted(): void
    {
        $this->forceFill([
            'accepted_at' => now(),
        ])->save();
    }
}
