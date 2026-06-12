<?php

declare(strict_types=1);

use App\Http\Controllers\Teams\TeamDestroyController;
use App\Http\Controllers\Teams\TeamInvitationDestroyController;
use App\Http\Controllers\Teams\TeamInvitationMailAcceptController;
use App\Http\Controllers\Teams\TeamInvitationsAcceptController;
use App\Http\Controllers\Teams\TeamInvitationSendController;
use App\Http\Controllers\Teams\TeamMemberDestroyController;
use App\Http\Controllers\Teams\TeamMemberRoleUpdateController;
use App\Http\Controllers\Teams\TeamOwnershipInvitationMailAcceptController;
use App\Http\Controllers\Teams\TeamOwnershipInvitationSendController;
use App\Http\Controllers\Teams\TeamSettingsShowController;
use App\Http\Controllers\Teams\TeamStoreController;
use App\Http\Controllers\Teams\TeamSwitchController;
use App\Http\Controllers\Teams\TeamUpdateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'nossr'])->group(function (): void {

    Route::post('teams', TeamStoreController::class)
        ->name('teams.store');

    Route::put('current-team', TeamSwitchController::class)
        ->name('teams.current.update');

    Route::post('team-invitations', TeamInvitationsAcceptController::class)
        ->name('teams.invitations.accept');

    Route::get('team-invitations/{invitation}', TeamInvitationMailAcceptController::class)
        ->middleware(['signed'])
        ->name('team.invitations.mail.accept');

    Route::get('/team-ownership-transfers/{token}', TeamOwnershipInvitationMailAcceptController::class)
        ->middleware(['signed'])
        ->name('teams.ownership.invitations.mail.accept');

    Route::middleware(['has.team'])->group(function (): void {
        Route::delete('team-invitations/{invitation}', TeamInvitationDestroyController::class)
            ->name('teams.invitations.destroy');
    });

    Route::middleware(['has.team', 'team.member'])->group(function (): void {
        Route::get('teams/{team}', TeamSettingsShowController::class)
            ->name('teams.settings.show');

        Route::put('teams/{team}', TeamUpdateController::class)
            ->name('teams.update');

        Route::delete('teams/{team}', TeamDestroyController::class)
            ->middleware(['has.password'])
            ->name('teams.destroy');

        Route::post('teams/{team}/members', TeamInvitationSendController::class)
            ->name('teams.members.store');

        Route::put('teams/{team}/members/{user}', TeamMemberRoleUpdateController::class)
            ->name('teams.members.update');

        Route::delete('teams/{team}/members/{user}', TeamMemberDestroyController::class)
            ->name('teams.members.destroy');

        Route::post('/teams/{team}/transfer-ownership', TeamOwnershipInvitationSendController::class)
            ->name('teams.ownership.invitations.send');
    });
});
