<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\ProfileDestroyRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;

final readonly class ProfileDestroyController
{
    public function __invoke(
        ProfileDestroyRequest $request,
        StatefulGuard $guard
    ): RedirectResponse {
        /** @var User $user */
        $user = $request->user();

        if ($user->ownedTeams()->exists()) {
            return back()->with('error', 'You must delete the teams you own before deleting your account');
        }

        $user->delete();
        $guard->logout();
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return to_route('home')->with('success', 'Your account has been deleted.');
    }
}
