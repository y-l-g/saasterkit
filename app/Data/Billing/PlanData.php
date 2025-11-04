<?php

declare(strict_types=1);

namespace App\Data\Billing;

use App\Enums\Billing\FeatureEnum;
use App\Enums\Billing\PlanEnum;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class PlanData extends Data
{
    /**
     * @param  array<FeatureEnum>  $features
     * @param  array<string, int>  $limits
     * @param  array<string, string>  $prices
     */
    public function __construct(
        public readonly PlanEnum $name,
        public readonly array $features,
        public readonly array $limits,
        public readonly array $prices,
    ) {}
}
