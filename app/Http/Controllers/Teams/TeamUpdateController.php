<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teams;

use App\Http\Requests\Teams\TeamUpdateRequest;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;

final readonly class TeamUpdateController
{
    public function __invoke(TeamUpdateRequest $request, Team $team): RedirectResponse
    {
        $team->forceFill([
            'name' => $request->input('name'),
        ])->save();

        return back()->with('success', 'Team name has been updated.');
    }
}
