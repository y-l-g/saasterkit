<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamDestroyRequest;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;

final readonly class TeamDestroyController
{
    public function __invoke(TeamDestroyRequest $request, Team $current_team): RedirectResponse
    {
        $team = $current_team;

        $team->purge();

        $newTeam = $request->user()->teams()->first();
        if ($newTeam) {
            $request->user()->switchToTeam($newTeam);

            return to_route('dashboard', ['current_team' => $newTeam->slug])
                ->with('success', 'Team has been deleted.');
        }

        return to_route('onboarding')->with('success', 'Team has been deleted.');
    }
}
