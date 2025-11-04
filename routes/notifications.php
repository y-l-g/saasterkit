<?php

declare(strict_types=1);

use App\Http\Controllers\AppNotifications\AppNotificationDismissController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::post('notifications/{notification}/dismiss', AppNotificationDismissController::class)->name('notifications.dismiss');
});
