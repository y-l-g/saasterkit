<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use App\Data\Billing\BillingSettingsPageData;
use App\Data\Billing\InvoiceData;
use App\Data\Billing\PlanData;
use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Invoice;

final readonly class ShowBillingSettingsController
{
    public function __construct(
        private PlanService $planService
    ) {}

    public function __invoke(Request $request, Team $team): Response
    {
        Gate::authorize(TeamMemberPermissionEnum::BILLING_SETTINGS_VIEW, $team);

        $invoices = $team->invoices(false, ['limit' => 24])->map(function (Invoice $invoice): array {
            $stripeInvoice = $invoice->asStripeInvoice();

            return [
                'date' => $invoice->date(),
                'total' => $invoice->total(),
                'url' => $stripeInvoice->invoice_pdf,
                'status' => $stripeInvoice->status,
                'id' => $stripeInvoice->id,
                'number' => $stripeInvoice->number,
            ];
        });

        $invoicesData = InvoiceData::collect($invoices, Collection::class);

        return Inertia::render(
            'settings/Billing',
            BillingSettingsPageData::from([
                'plans' => PlanData::collect($this->planService->all()),
                'invoices' => $invoicesData->values(),
                'team' => $team->load('defaultSubscription'),
            ])
        );
    }
}
