<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Enums\Auth\AuthEnum;
use App\Models\TeamInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final readonly class TeamInvitationMailAcceptController
{
    public function __invoke(Request $request, TeamInvitation $invitation): RedirectResponse
    {
        $user = $request->user();

        Gate::authorize(AuthEnum::ACCEPT_TEAM_INVITATION, $invitation);

        DB::transaction(function () use ($user, $invitation): void {
            $team = $invitation->team;

            if (! $team->hasUser($user)) {
                $team->users()->attach($user, ['role' => $invitation->role]);
            }
            $user->switchToTeam($team);
            $invitation->delete();
        });

        return to_route('dashboard')->with('success', "Great! You have joined the {$invitation->team->name} team.");
    }
}
