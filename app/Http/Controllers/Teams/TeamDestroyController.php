<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamDestroyRequest;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;

final readonly class TeamDestroyController
{
    public function __invoke(TeamDestroyRequest $request, Team $team): RedirectResponse
    {
        $defaultSubscription = $team->subscription('default');

        if ($defaultSubscription !== null && $defaultSubscription->valid() && ! $defaultSubscription->canceled()) {
            return back()->with('error', 'You must cancel your subscription before delete this team');
        }

        $team->purge();

        return to_route('dashboard')->with('success', 'Team has been deleted.');
    }
}
