<?php

declare(strict_types=1);

use App\Http\Controllers\Teams\TeamDestroyController;
use App\Http\Controllers\Teams\TeamInvitationSendController;
use App\Http\Controllers\Teams\TeamMemberDestroyController;
use App\Http\Controllers\Teams\TeamMemberRoleUpdateController;
use App\Http\Controllers\Teams\TeamOwnershipInvitationSendController;
use App\Http\Controllers\Teams\TeamSettingsShowController;
use App\Http\Controllers\Teams\TeamUpdateController;
use Illuminate\Support\Facades\Route;

Route::get('settings/team', TeamSettingsShowController::class)
    ->name('teams.settings.show');

Route::put('settings/team', TeamUpdateController::class)
    ->name('teams.update');

Route::delete('settings/team', TeamDestroyController::class)
    ->middleware(['has.password'])
    ->name('teams.destroy');

Route::post('settings/team/members', TeamInvitationSendController::class)
    ->name('teams.members.store');

Route::put('settings/team/members/{user}', TeamMemberRoleUpdateController::class)
    ->name('teams.members.update');

Route::delete('settings/team/members/{user}', TeamMemberDestroyController::class)
    ->name('teams.members.destroy');

Route::post('settings/team/transfer-ownership', TeamOwnershipInvitationSendController::class)
    ->name('teams.ownership.invitations.send');
