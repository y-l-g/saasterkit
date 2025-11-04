<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamInvitationsAcceptRequest;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

final readonly class TeamInvitationsAcceptController
{
    public function __invoke(TeamInvitationsAcceptRequest $request): RedirectResponse
    {

        /** @var User $user */
        $user = $request->user();
        $invitations = TeamInvitation::with('team')
            ->whereIn('id', $request->validated('invitations'))
            ->get();

        DB::transaction(function () use ($user, $invitations): void {
            $userTeamIds = $user->teams()->pluck('teams.id');
            $newInvitations = $invitations->whereNotIn('team.id', $userTeamIds);
            $teamsToAttach = $newInvitations->mapWithKeys(fn (TeamInvitation $invitation) => [
                $invitation->team_id => ['role' => $invitation->role],
            ]);
            if ($teamsToAttach->isNotEmpty()) {
                $user->teams()->attach($teamsToAttach->all());
            }

            TeamInvitation::query()->whereIn('id', $invitations->pluck('id'))->delete();
        });

        $user->refresh();

        $lastInvitedTeam = $invitations->last()?->team;
        if ($lastInvitedTeam && $user->current_team_id === null) {
            $user->switchToTeam($lastInvitedTeam);
        }

        return to_route('dashboard')->with('success', 'Great! You have joined the team.');
    }
}
