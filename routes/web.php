<?php

declare(strict_types=1);

use App\Http\Controllers\App\AppDashboardShowController;
use App\Http\Controllers\App\AppOnboardingShowController;
use App\Http\Controllers\Public\PrivacyController;
use App\Http\Controllers\Public\WelcomeController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('home');

Route::get('privacy', PrivacyController::class)->name('privacy');

Route::middleware(['auth', 'verified', 'nossr'])->group(function (): void {

    Route::get('dashboard', function (): RedirectResponse {
        $team = request()->user()?->currentTeam;

        if (! $team) {
            return to_route('onboarding');
        }

        return to_route('dashboard', ['current_team' => $team->slug]);
    })->middleware(['has.team']);

    Route::get('settings', function (): RedirectResponse {
        $team = request()->user()?->currentTeam;

        if (! $team) {
            return to_route('onboarding');
        }

        return to_route('user.settings', ['current_team' => $team->slug]);
    })->middleware(['has.team']);

    Route::get('onboarding', AppOnboardingShowController::class)->name('onboarding');

});

require __DIR__.'/notifications.php';
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/teams.php';

Route::prefix('{current_team:slug}')
    ->middleware(['auth', 'verified', 'has.team', EnsureTeamMembership::class, 'nossr'])
    ->group(function (): void {
        Route::get('dashboard', AppDashboardShowController::class)->name('dashboard');

        require __DIR__.'/settings.php';
        require __DIR__.'/billing.php';
        require __DIR__.'/current-team.php';
    });
