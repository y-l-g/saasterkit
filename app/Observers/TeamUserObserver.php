<?php

namespace App\Observers;

use App\Models\TeamUser;
use Illuminate\Support\Facades\Cache;

class TeamUserObserver
{
    /**
     * Handle the TeamUser "created" event.
     */
    public function created(TeamUser $teamUser): void
    {
        $this->clearPermissionsCache($teamUser);
    }

    /**
     * Handle the TeamUser "updated" event.
     */
    public function updated(TeamUser $teamUser): void
    {
        $this->clearPermissionsCache($teamUser);
    }

    /**
     * Handle the TeamUser "deleted" event.
     */
    public function deleted(TeamUser $teamUser): void
    {
        $this->clearPermissionsCache($teamUser);
    }

    protected function clearPermissionsCache(TeamUser $teamUser): void
    {
        $cacheKey = "user.{$teamUser->user_id}.team.{$teamUser->team_id}.permissions";
        Cache::forget($cacheKey);
    }
}
