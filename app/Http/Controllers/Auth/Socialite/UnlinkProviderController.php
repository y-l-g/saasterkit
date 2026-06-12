<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Socialite;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class UnlinkProviderController
{
    public function __invoke(Request $request, string $provider): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->socialAccounts()
            ->where('provider', $provider)
            ->firstOrFail()
            ->delete();

        if (! $user->currentTeam) {
            return to_route('onboarding')->with('success', 'The social account has been unlinked successfully.');
        }

        return to_route('profile.edit', ['current_team' => $user->currentTeam->slug])
            ->with('success', 'The social account has been unlinked successfully.');
    }
}
