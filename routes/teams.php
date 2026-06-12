<?php

declare(strict_types=1);

use App\Http\Controllers\Teams\TeamInvitationDestroyController;
use App\Http\Controllers\Teams\TeamInvitationMailAcceptController;
use App\Http\Controllers\Teams\TeamInvitationsAcceptController;
use App\Http\Controllers\Teams\TeamOwnershipInvitationMailAcceptController;
use App\Http\Controllers\Teams\TeamStoreController;
use App\Http\Controllers\Teams\TeamSwitchController;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
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

    Route::get('teams/{team:slug}', fn (Team $team): RedirectResponse => to_route('teams.settings.show', [
        'current_team' => $team->slug,
    ]))->middleware(['has.team', 'team.member']);
});
