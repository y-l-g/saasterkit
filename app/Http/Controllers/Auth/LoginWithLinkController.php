<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class LoginWithLinkController
{
    public function __construct(
        private StatefulGuard $guard,
    ) {}

    public function __invoke(Request $request, User $user): RedirectResponse
    {
        $this->guard->login($user);

        $request->session()->regenerate();

        return to_route('profile.edit')->with('success', 'You have been logged in successfully.');
    }
}
