<?php

declare(strict_types=1);

namespace App\Enums\Billing;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
enum SubscriptionStatusEnum: string
{
    case STATUS_ACTIVE = 'active';
    case STATUS_CANCELED = 'canceled';
    case STATUS_INCOMPLETE = 'incomplete';
    case STATUS_INCOMPLETE_EXPIRED = 'incomplete_expired';
    case STATUS_PAST_DUE = 'past_due';
    case STATUS_PAUSED = 'paused';
    case STATUS_TRIALING = 'trialing';
    case STATUS_UNPAID = 'unpaid';
}
