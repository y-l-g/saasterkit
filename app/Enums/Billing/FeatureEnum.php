<?php

declare(strict_types=1);

namespace App\Enums\Billing;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript()]
enum FeatureEnum: string
{
    case PRO_FEATURE_1 = 'pro_feature1';
    case PRO_FEATURE_2 = 'pro_feature2';
    case PRO_FEATURE_3 = 'pro_feature3';
    case PREMIUM_FEATURE_1 = 'premium_feature1';
    case PREMIUM_FEATURE_2 = 'premium_feature2';
    case PREMIUM_FEATURE_3 = 'premium_feature3';

}
