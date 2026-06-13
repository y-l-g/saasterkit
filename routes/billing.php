<?php

declare(strict_types=1);

use App\Http\Controllers\Billing\CreateStripeCheckoutController;
use App\Http\Controllers\Billing\RedirectToBillingPortalController;
use App\Http\Controllers\Billing\ShowBillingSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('settings/billing', ShowBillingSettingsController::class)->name('billing.show');

Route::post('settings/billing/checkout/{stripePriceId}', CreateStripeCheckoutController::class)
    ->name('billing.checkout');

Route::post('settings/billing/portal', RedirectToBillingPortalController::class)
    ->name('billing.portal');
