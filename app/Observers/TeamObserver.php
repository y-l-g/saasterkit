<?php

namespace App\Observers;

use App\Models\Team;
use Illuminate\Support\Facades\Cache;

class TeamObserver
{
    /**
     * Handle the Team "updated" event.
     */
    public function updated(Team $team): void
    {
        if ($team->isDirty('user_id')) {
            $oldOwnerId = $team->getOriginal('user_id');
            if ($oldOwnerId) {
                Cache::forget("user.{$oldOwnerId}.team.{$team->id}.permissions");
            }
            Cache::forget("user.{$team->user_id}.team.{$team->id}.permissions");
        }
    }
}
