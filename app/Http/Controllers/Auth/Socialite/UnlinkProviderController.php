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

        return to_route('profile.edit')->with('success', 'The social account has been unlinked successfully.');
    }
}
