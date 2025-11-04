<?php

declare(strict_types=1);

namespace App\Enums\Billing;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
enum PlanEnum: string
{
    case PRO = 'pro';
    case PREMIUM = 'premium';

    /**
     * @return FeatureEnum[]
     */
    public function features(): array
    {
        return match ($this) {
            self::PRO => [
                FeatureEnum::PRO_FEATURE_1,
                FeatureEnum::PRO_FEATURE_2,
                FeatureEnum::PRO_FEATURE_3,
            ],
            self::PREMIUM => [
                FeatureEnum::PRO_FEATURE_1,
                FeatureEnum::PRO_FEATURE_2,
                FeatureEnum::PRO_FEATURE_3,
                FeatureEnum::PREMIUM_FEATURE_1,
                FeatureEnum::PREMIUM_FEATURE_2,
                FeatureEnum::PREMIUM_FEATURE_3,
            ],
        };
    }

    /**
     * @return array<string, int>
     */
    public function limits(): array
    {
        return match ($this) {
            self::PRO => ['projects' => 10],
            self::PREMIUM => ['projects' => 50],
        };
    }

    /**
     * @return array<string, string|null>
     */
    public function prices(): array
    {
        return [
            'month' => config('prices.stripe_price_'.$this->value.'_month'),
            'year' => config('prices.stripe_price_'.$this->value.'_year'),
        ];
    }
}
