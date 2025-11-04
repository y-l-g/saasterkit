<?php

declare(strict_types=1);

namespace App\Data\Billing;

use App\Enums\Billing\InvoiceStatusEnum;
use DateTime;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class InvoiceData extends Data
{
    public function __construct(
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly DateTime $date,
        public readonly string $total,
        public readonly string $url,
        #[WithCast(EnumCast::class)]
        public readonly InvoiceStatusEnum $status,
        public readonly string $id,
        public readonly string $number,
    ) {}
}
