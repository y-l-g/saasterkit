<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamInvitationSendRequest;
use App\Mail\TeamInvitationMail;
use App\Models\Team;
use App\Models\User;
use App\Services\AppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

final readonly class TeamInvitationSendController
{
    public function __construct(
        private AppNotificationService $notificationService
    ) {}

    public function __invoke(TeamInvitationSendRequest $request, Team $team): RedirectResponse
    {
        $invitation = $team->teamInvitations()->create([
            'email' => $request->input('email'),
            'role' => $request->input('role'),
        ]);

        $user = User::query()->where('email', $request->input('email'))->first();
        if ($user) {
            $this->notificationService->sendToUser(
                user: $user,
                title: 'Team invitation',
                body: "You have been invited to join the team {$team->name}."
            );
        }

        Mail::to($request->input('email'))->queue(new TeamInvitationMail($invitation));

        return back()->with('success', 'Invitation sent.');
    }
}
