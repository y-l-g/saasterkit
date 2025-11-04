<?php

declare(strict_types=1);

namespace App\Enums\Billing;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
enum BillingPeriodEnum: string
{
    case MONTH = 'month';
    case YEAR = 'year';

}
