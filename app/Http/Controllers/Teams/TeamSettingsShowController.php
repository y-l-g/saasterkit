<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Data\Teams\TeamData;
use App\Data\Teams\TeamMemberData;
use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final readonly class TeamSettingsShowController
{
    public function __construct(private RoleService $roleService) {}

    public function __invoke(Request $request, Team $team): Response
    {
        Gate::authorize(TeamMemberPermissionEnum::TEAM_VIEW, $team);

        $request->user()->switchToTeam($team);

        $team->load('owner', 'teamInvitations', 'defaultSubscription');

        return Inertia::render('teams/TeamSettings', [
            'team' => TeamData::from($team),
            'members' => TeamMemberData::collect($team->users()->paginate()),
            'availableRoles' => $this->roleService->all(),
        ]);
    }
}
