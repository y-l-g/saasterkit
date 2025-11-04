<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminAppNotificationIndexController;
use App\Http\Controllers\Admin\AdminAppNotificationStoreController;
use App\Http\Controllers\Admin\AdminDashboardShowController;
use App\Http\Controllers\Admin\AdminSubscriptionIndexShowController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->group(function (): void {

    Route::redirect('admin', '/admin/dashboard')->name('admin');

    Route::get('admin/subscriptions', AdminSubscriptionIndexShowController::class)->name('admin.subscriptions.index');

    Route::get('admin/dashboard', AdminDashboardShowController::class)->name('admin.dashboard');

    Route::get('admin/notifications', AdminAppNotificationIndexController::class)->name('admin.notifications.index');

    Route::post('admin/notifications', AdminAppNotificationStoreController::class)->name('admin.notifications.store');

});
