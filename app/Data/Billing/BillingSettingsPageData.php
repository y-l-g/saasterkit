<?php

declare(strict_types=1);

namespace App\Data\Billing;

use App\Data\Teams\TeamData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\AutoInertiaDeferred;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class BillingSettingsPageData extends Data
{
    /**
     * @param  Collection<int, PlanData>  $plans
     * @param  Lazy|Collection<int, InvoiceData>  $invoices
     */
    public function __construct(
        /** @var Collection<int, PlanData> */
        public readonly Collection $plans,
        #[AutoInertiaDeferred]
        /** @var Lazy|Collection<int, InvoiceData> */
        public readonly Lazy|Collection $invoices,
        public readonly TeamData $team,
    ) {}
}
