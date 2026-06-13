<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;

final readonly class LoginWithLinkController
{
    public function __construct(
        private StatefulGuard $guard,
    ) {}

    public function __invoke(Request $request, User $user): RedirectResponse
    {
        if ($user->hasEnabledTwoFactorAuthentication()) {
            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => false,
            ]);

            event(new TwoFactorAuthenticationChallenged($user));

            return to_route('two-factor.login');
        }

        $this->guard->login($user);

        $request->session()->regenerate();

        if (! $user->currentTeam) {
            return to_route('onboarding')->with('success', 'You have been logged in successfully.');
        }

        return to_route('profile.edit', ['current_team' => $user->currentTeam->slug])
            ->with('success', 'You have been logged in successfully.');
    }
}
