<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamSwitchRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

final readonly class TeamSwitchController
{
    public function __invoke(TeamSwitchRequest $request): RedirectResponse
    {
        /** @var Team $team */
        $team = Team::query()->findOrFail($request->input('team_id'));
        /** @var User $user */
        $user = $request->user();

        $user->switchToTeam($team);

        return to_route('dashboard');
    }
}
