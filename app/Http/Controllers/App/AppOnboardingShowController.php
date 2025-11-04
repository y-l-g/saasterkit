<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Data\Teams\TeamInvitationData;
use App\Http\Controllers\Controller;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppOnboardingShowController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('app/Onboarding', [
            'invitations' => TeamInvitationData::collect(
                TeamInvitation::with('team')
                    ->where('email', $request->user()->email)
                    ->get()
            ),
        ]);
    }
}
