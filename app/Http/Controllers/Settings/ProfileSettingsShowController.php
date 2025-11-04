<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Enums\Auth\SocialiteProviderEnum;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class ProfileSettingsShowController
{
    public function __invoke(Request $request): Response
    {
        $userOwnsTeam = $request->user()->ownedTeams()->exists();

        return Inertia::render('settings/Profile', [
            'availableProviders' => SocialiteProviderEnum::cases(),
            'linkedProviders' => $request->user()->socialAccounts()->get()->pluck('provider'),
            'userOwnsTeam' => $userOwnsTeam,
        ]);
    }
}
