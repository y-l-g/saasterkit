<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use App\Data\Billing\BillingSettingsPageData;
use App\Data\Billing\InvoiceData;
use App\Data\Billing\PlanData;
use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use App\Services\PlanService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Invoice;
use Spatie\LaravelData\Lazy;

final readonly class ShowBillingSettingsController
{
    public function __construct(
        private PlanService $planService
    ) {}

    public function __invoke(Team $current_team): Response
    {
        $team = $current_team;

        Gate::authorize(TeamMemberPermissionEnum::BILLING_SETTINGS_VIEW, $team);

        return Inertia::render(
            'settings/Billing',
            BillingSettingsPageData::from([
                'plans' => PlanData::collect($this->planService->all()),
                'invoices' => Lazy::inertiaDeferred(fn (): Collection => $this->invoices($team)),
                'team' => $team->load('defaultSubscription'),
            ])
        );
    }

    /**
     * @return Collection<int, InvoiceData>
     */
    private function invoices(Team $team): Collection
    {
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

        return InvoiceData::collect($invoices, Collection::class)->values();
    }
}
