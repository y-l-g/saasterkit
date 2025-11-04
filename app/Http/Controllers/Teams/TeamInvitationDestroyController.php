<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamInvitationDestroyRequest;
use App\Models\TeamInvitation;
use Illuminate\Http\RedirectResponse;

final readonly class TeamInvitationDestroyController
{
    public function __invoke(TeamInvitationDestroyRequest $request, TeamInvitation $invitation): RedirectResponse
    {
        $invitation->delete();

        return back()->with('success', 'Invitation has been canceled.');
    }
}
