<?php

declare(strict_types=1);

namespace App\Data\Billing;

use App\Data\Teams\TeamData;
use App\Enums\Billing\BillingPeriodEnum;
use App\Enums\Billing\SubscriptionStatusEnum;
use App\Models\Subscription;
use DateTime;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class SubscriptionData extends Data
{
    public function __construct(
        public readonly int $id,
        #[WithCast(EnumCast::class)]
        public readonly SubscriptionStatusEnum $status,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?DateTime $endsAt,
        public readonly bool $onGracePeriod,
        public readonly PlanData $plan,
        public readonly string $stripePriceId,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?DateTime $createdAt,
        #[WithCast(DateTimeInterfaceCast::class)]
        public readonly ?DateTime $trialEndsAt,
        public readonly Lazy|TeamData $team,
        public readonly BillingPeriodEnum $period
    ) {}

    public static function fromModel(Subscription $subscription): self
    {
        $periodKey = array_search($subscription->stripe_price, $subscription->planData->prices, true);

        return new self(
            id: $subscription->id,
            status: SubscriptionStatusEnum::from($subscription->stripe_status),
            endsAt: $subscription->ends_at,
            onGracePeriod: (bool) $subscription->onGracePeriod(),
            plan: $subscription->planData,
            stripePriceId: $subscription->stripe_price,
            createdAt: $subscription->created_at,
            trialEndsAt: $subscription->trial_ends_at,
            team: Lazy::whenLoaded('team', $subscription, fn (): TeamData => TeamData::from($subscription->team)),
            period: $periodKey === 'year' ? BillingPeriodEnum::YEAR : BillingPeriodEnum::MONTH
        );
    }
}
