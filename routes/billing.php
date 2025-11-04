<?php

declare(strict_types=1);

use App\Http\Controllers\Billing\CreateStripeCheckoutController;
use App\Http\Controllers\Billing\RedirectToBillingPortalController;
use App\Http\Controllers\Billing\ShowBillingSettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'has.team'])->group(function (): void {

    Route::get('billing/{team}', ShowBillingSettingsController::class)->name('billing.show');

    Route::get('checkout/{stripePriceId}/{team}', CreateStripeCheckoutController::class)->name('billing.checkout');

    Route::get('billing/portal/{team}', RedirectToBillingPortalController::class)
        ->name('billing.portal');
});
