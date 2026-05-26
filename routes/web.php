<?php

declare(strict_types=1);

use App\Http\Controllers\App\AppDashboardShowController;
use App\Http\Controllers\App\AppOnboardingShowController;
use App\Http\Controllers\Public\PrivacyController;
use App\Http\Controllers\Public\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('home');

Route::get('privacy', PrivacyController::class)->name('privacy');

Route::middleware(['auth', 'verified', 'nossr'])->group(function (): void {

    Route::get('dashboard', AppDashboardShowController::class)->middleware(['has.team'])->name('dashboard');

    Route::get('onboarding', AppOnboardingShowController::class)->name('onboarding');

});

require __DIR__.'/notifications.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/billing.php';
require __DIR__.'/admin.php';
require __DIR__.'/teams.php';
