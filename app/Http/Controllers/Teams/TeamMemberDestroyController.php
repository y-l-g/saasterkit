<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamMemberDestroyRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

final readonly class TeamMemberDestroyController
{
    public function __invoke(TeamMemberDestroyRequest $request, Team $current_team, User $user): RedirectResponse
    {
        $current_team->removeUser($user);

        /** @var User $currentUser */
        $currentUser = $request->user();

        if ($currentUser->is($user)) {
            $fallbackTeam = $currentUser->teams()->first();

            if ($fallbackTeam) {
                $currentUser->switchToTeam($fallbackTeam);

                return to_route('dashboard', ['current_team' => $fallbackTeam->slug])
                    ->with('success', 'You have left the team.');
            }

            return to_route('onboarding')->with('success', 'You have left the team.');
        }

        return back()->with('success', 'Team member has been removed.');
    }
}
