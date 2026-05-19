<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $new_owner_email
 * @property string $token
 * @property int $team_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Team $team
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamOwnershipInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamOwnershipInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamOwnershipInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamOwnershipInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamOwnershipInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamOwnershipInvitation whereNewOwnerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamOwnershipInvitation whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamOwnershipInvitation whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamOwnershipInvitation whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[Fillable([
    'team_id',
    'new_owner_email',
    'token',
])]
class TeamOwnershipInvitation extends Model
{
    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
