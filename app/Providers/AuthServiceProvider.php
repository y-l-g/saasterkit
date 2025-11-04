<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\Auth\AuthEnum;
use App\Enums\Billing\FeatureEnum;
use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamOwnershipInvitation;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            return $user->isAdmin() ? true : null;
        });

        Gate::define(AuthEnum::SEND_APP_NOTIFICATION, function (User $user) {
            return $user->isAdmin();
        });

        Gate::define(AuthEnum::ACCEPT_TEAM_INVITATION, function (User $user, TeamInvitation $invitation) {
            return $user->email === $invitation->email
                ? Response::allow()
                : Response::deny("You're trying to accept an invitation with the wrong account. Please log in with the email address to which the invitation was sent.");
        });

        Gate::define(AuthEnum::ACCEPT_TEAM_OWNERSHIP_INVITATION, function (User $user, TeamOwnershipInvitation $invitation) {
            return $user->email === $invitation->new_owner_email
                ? Response::allow()
                : Response::deny("You're trying to accept an invitation with the wrong account. Please log in with the email address to which the invitation was sent.");
        });

        $this->registerTeamPermissionGates();
        $this->registerFeatureGates();
    }

    private function registerTeamPermissionGates(): void
    {
        foreach (TeamMemberPermissionEnum::cases() as $permission) {
            Gate::define($permission->value, function (User $user, Team $team) use ($permission) {
                return $user->hasTeamPermission($team, $permission);
            });
        }
    }

    private function registerFeatureGates(): void
    {
        foreach (FeatureEnum::cases() as $feature) {
            Gate::define($feature->value, function (User $user, Team $team) use ($feature) {
                return $user->belongsToTeam($team) && $team->hasFeature($feature);
            });
        }
    }
}
