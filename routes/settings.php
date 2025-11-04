<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\ProfileDestroyController;
use App\Http\Controllers\Settings\AppearanceUpdateController;
use App\Http\Controllers\Settings\PasswordSettingsShowController;
use App\Http\Controllers\Settings\ProfileSettingsShowController;
use App\Http\Controllers\Settings\TwoFactorSettingsShowController;
use App\Http\Controllers\Settings\UserTeamIndexShowController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function (): void {

    Route::redirect('settings', '/settings/profile')->name('user.settings');

    Route::get('settings/profile', ProfileSettingsShowController::class)
        ->name('profile.edit');

    Route::delete('settings/profile', ProfileDestroyController::class)
        ->name('profile.destroy')->middleware(['has.password']);

    Route::get('settings/password', PasswordSettingsShowController::class)
        ->name('password.edit');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    Route::patch('settings/appearance', AppearanceUpdateController::class)
        ->name('appearance.update');

    Route::get('settings/two-factor', TwoFactorSettingsShowController::class)
        ->middleware(['has.password', 'password.confirm'])
        ->name('two-factor.show');

    Route::get('settings/teams', UserTeamIndexShowController::class)->name('user.teams');

});
