<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Enums\Auth\AuthEnum;
use App\Enums\Teams\RoleEnum;
use App\Models\TeamOwnershipInvitation;
use App\Models\User;
use App\Services\AppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class TeamOwnershipInvitationMailAcceptController
{
    public function __construct(private AppNotificationService $notificationService) {}

    public function __invoke(
        Request $request,
        string $token,
    ): RedirectResponse {
        $invitation = TeamOwnershipInvitation::query()->where('token', $token)->firstOrFail();

        /** @var User $user */
        $user = $request->user();

        Gate::authorize(AuthEnum::ACCEPT_TEAM_OWNERSHIP_INVITATION, $invitation);

        $team = $invitation->team;
        $oldOwner = $team->owner;

        DB::transaction(function () use ($user, $invitation, $team): void {
            $team->users()->updateExistingPivot($team->owner->id, [
                'role' => RoleEnum::ADMIN,
            ]);
            $team->owner()->associate($user)->save();
            $user->switchToTeam($team);
            $invitation->delete();
        });

        $this->notificationService->sendToUser(
            user: $oldOwner,
            title: "Team ownership change for team \"{$team->name}\"",
            body: "You are no more owner of the team {$team->name}. The new owner is {$user->email}."
        );

        return to_route('dashboard')->with('success', "You are now the owner of the {$team->name} team.");
    }
}
