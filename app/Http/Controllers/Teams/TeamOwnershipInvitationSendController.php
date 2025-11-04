<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamOwnershipInvitationSendRequest;
use App\Mail\TeamOwnershipInvitationMail;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

final class TeamOwnershipInvitationSendController
{
    public function __invoke(TeamOwnershipInvitationSendRequest $request, Team $team): RedirectResponse
    {
        $team->ownershipInvitations()->delete();

        $transfer = $team->ownershipInvitations()->create([
            'new_owner_email' => $request->input('email'),
            'token' => Str::uuid()->toString(),
        ]);

        Mail::to($request->input('email'))->queue(new TeamOwnershipInvitationMail($transfer));

        return back()->with('success', 'Team ownership transfer invitation has been sent.');
    }
}
