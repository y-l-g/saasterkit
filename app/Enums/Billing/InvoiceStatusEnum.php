<?php

declare(strict_types=1);

namespace App\Enums\Billing;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
enum InvoiceStatusEnum: string
{
    case PAID = 'paid';
    case DRAFT = 'draft';
    case OPEN = 'open';
}
