<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Socialite;

use Illuminate\Http\RedirectResponse as LaravelRedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

final readonly class RedirectToProviderController
{
    public function __invoke(string $provider): RedirectResponse|LaravelRedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }
}
