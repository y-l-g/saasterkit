<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginWithLinkController;
use App\Http\Controllers\Auth\Socialite\HandleProviderCallbackController;
use App\Http\Controllers\Auth\Socialite\RedirectToProviderController;
use App\Http\Controllers\Auth\Socialite\UnlinkProviderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:6,1'])->group(function (): void {

    Route::middleware('guest')->group(function (): void {

        Route::get('auth/login/{user}', LoginWithLinkController::class)
            ->middleware(['signed'])
            ->name('auth.login.link');

    });

    Route::middleware(['auth', 'verified'])->group(function (): void {

        Route::get('auth/{provider}/link', RedirectToProviderController::class)->name('provider.link');

        Route::delete('auth/{provider}/unlink', UnlinkProviderController::class)->name('provider.unlink');

    });

    Route::get('auth/{provider}/redirect', RedirectToProviderController::class)->name('provider.redirect');

    Route::get('auth/{provider}/callback', HandleProviderCallbackController::class)->name('provider.callback');

});
