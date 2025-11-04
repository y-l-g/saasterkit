<?php

namespace App\Models\Concerns;

use App\Enums\Billing\FeatureEnum;
use App\Models\Subscription;
use App\Services\PlanService;

trait HasPlanFeatures
{
    public function hasFeature(FeatureEnum $feature): bool
    {
        /** @var Subscription|null $subscription */
        $subscription = $this->subscriptions()
            ->where('type', 'default')
            ->first();

        if (! $subscription?->valid()) {
            return false;
        }
        $plan = app(PlanService::class)->getPlanByStripePriceId($subscription->stripe_price);

        if (! $plan) {
            return false;
        }

        return in_array($feature, $plan->features, true);
    }
}
