<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamMemberRoleUpdateRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

final readonly class TeamMemberRoleUpdateController
{
    public function __invoke(TeamMemberRoleUpdateRequest $request, Team $team, User $user): RedirectResponse
    {
        $team->users()->updateExistingPivot($user->id, [
            'role' => $request->input('role'),
        ]);

        return back()->with('success', 'Team member role has been updated.');
    }
}
