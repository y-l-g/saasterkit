<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Billing\PlanData;
use App\Enums\Billing\PlanEnum;
use App\Exceptions\PlanNotFoundException;
use Illuminate\Support\Collection;

final class PlanService
{
    /**
     * @return Collection<int, PlanData>
     */
    public function all(): Collection
    {
        return collect(PlanEnum::cases())
            ->map(fn (PlanEnum $plan): PlanData => new PlanData(
                name: $plan,
                features: $plan->features(),
                limits: $plan->limits(),
                prices: $plan->prices()
            ));
    }

    public function getPlanByStripePriceId(string $stripePriceId): ?PlanData
    {
        return $this->all()->first(function (PlanData $plan) use ($stripePriceId) {
            return in_array($stripePriceId, $plan->prices, true);
        });
    }

    /**
     * @throws PlanNotFoundException
     */
    public function findOrFailPlanByStripePriceId(string $stripePriceId): PlanData
    {
        $plan = $this->getPlanByStripePriceId($stripePriceId);
        throw_if(is_null($plan), PlanNotFoundException::class, "No plan found for Stripe price ID: {$stripePriceId}");

        return $plan;
    }

    public function priceIdExists(string $stripePriceId): bool
    {
        return $this->getPlanByStripePriceId($stripePriceId) !== null;
    }

    // public function getLimit(PlanEnum $plan, string $limitName): ?int
    // {
    //     $planData = $this->all()->firstWhere('name', $plan->value);

    //     if (!$planData instanceof PlanData) {
    //         return null;
    //     }

    //     return $planData->limits[$limitName] ?? null;
    // }
}
