<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Data\Teams\TeamInvitationData;
use App\Data\Teams\UserTeamIndexData;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserTeamIndexShowController extends Controller
{
    public function __invoke(Request $request): Response
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->load([
            'teams.users' => function ($query) use ($user): void {
                $query->where('users.id', $user->id);
            },
        ]);
        $teams = $user->teams->map(function (Team $team) use ($user) {
            return UserTeamIndexData::fromTeamForUser($team, $user);
        });

        return Inertia::render('settings/UserTeamIndex', [
            'teams' => $teams,
            'invitations' => TeamInvitationData::collect(
                TeamInvitation::with('team')
                    ->where('email', $user->email)
                    ->get(),
            ),
        ]);
    }
}
