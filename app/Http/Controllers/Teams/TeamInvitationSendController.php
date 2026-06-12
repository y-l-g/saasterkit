<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamInvitationSendRequest;
use App\Models\Team;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use App\Services\AppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

final readonly class TeamInvitationSendController
{
    public function __construct(
        private AppNotificationService $notificationService
    ) {}

    public function __invoke(TeamInvitationSendRequest $request, Team $current_team): RedirectResponse
    {
        $team = $current_team;

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

        Notification::route('mail', $invitation->email)
            ->notify(new TeamInvitationNotification($invitation));

        return back()->with('success', 'Invitation sent.');
    }
}
