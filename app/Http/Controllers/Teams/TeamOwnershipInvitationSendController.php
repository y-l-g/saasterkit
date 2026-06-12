<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamOwnershipInvitationSendRequest;
use App\Models\Team;
use App\Models\User;
use App\Notifications\TeamOwnershipTransferNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

final class TeamOwnershipInvitationSendController
{
    public function __invoke(TeamOwnershipInvitationSendRequest $request, Team $current_team): RedirectResponse
    {
        $team = $current_team;

        $newOwner = User::query()->where('email', $request->input('email'))->firstOrFail();

        $team->ownershipInvitations()->delete();

        $transfer = $team->ownershipInvitations()->create([
            'new_owner_email' => $request->input('email'),
            'token' => Str::uuid()->toString(),
        ]);

        $newOwner->notify(new TeamOwnershipTransferNotification($transfer));

        return back()->with('success', 'Team ownership transfer invitation has been sent.');
    }
}
