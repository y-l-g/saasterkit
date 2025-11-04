<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $new_owner_email
 * @property string $token
 * @property int $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Team $team
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
class TeamOwnershipInvitation extends Model
{
    protected $fillable = [
        'team_id',
        'new_owner_email',
        'token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
