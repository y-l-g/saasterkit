<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamStoreRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

final readonly class TeamStoreController
{
    public function __invoke(TeamStoreRequest $request): RedirectResponse
    {

        /** @var User $user */
        $user = $request->user();

        $team = DB::transaction(function () use ($user, $request) {
            $team = $user->ownedTeams()->create([
                'name' => $request->input('name'),
            ]);

            $user->teams()->attach($team);

            return $team;
        });

        $user->switchToTeam($team);

        return to_route('billing.show', $team);
    }
}
