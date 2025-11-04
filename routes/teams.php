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

Route::middleware(['has.team', 'auth', 'verified'])->group(function (): void {

    Route::post('teams', TeamStoreController::class)
        ->withoutMiddleware(['has.team'])
        ->name('teams.store');

    Route::get('teams/{team}', TeamSettingsShowController::class)
        ->name('teams.settings.show');

    Route::put('teams/{team}', TeamUpdateController::class)
        ->name('teams.update');

    Route::delete('teams/{team}', TeamDestroyController::class)
        ->middleware(['has.password'])
        ->name('teams.destroy');

    Route::put('current-team', TeamSwitchController::class)
        ->name('teams.current.update')->withoutMiddleware(['has.team']);

    Route::put('teams/{team}/members/{user}', TeamMemberRoleUpdateController::class)
        ->name('teams.members.update');
    Route::delete('teams/{team}/members/{user}', TeamMemberDestroyController::class)
        ->name('teams.members.destroy');

    Route::delete('team-invitations/{invitation}', TeamInvitationDestroyController::class)
        ->name('teams.invitations.destroy');
    Route::post('team-invitations', TeamInvitationsAcceptController::class)
        ->withoutMiddleware(['has.team'])
        ->name('teams.invitations.accept');

    Route::post('teams/{team}/members', TeamInvitationSendController::class)
        ->name('teams.members.store');

    Route::get('team-invitations/{invitation}', TeamInvitationMailAcceptController::class)
        ->withoutMiddleware(['has.team'])
        ->middleware(['signed'])
        ->name('team.invitations.mail.accept');

    Route::post('/teams/{team}/transfer-ownership', TeamOwnershipInvitationSendController::class)
        ->name('teams.ownership.invitations.send');

    Route::get('/team-ownership-transfers/{token}', TeamOwnershipInvitationMailAcceptController::class)
        ->middleware(['signed'])
        ->name('teams.ownership.invitations.mail.accept');
});
