<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Billing\PlanData;
use App\Services\PlanService;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\Subscription as CashierSubscription;

/**
 * @property int $id
 * @property int $team_id
 * @property string $type
 * @property string $stripe_id
 * @property string $stripe_status
 * @property string|null $stripe_price
 * @property int|null $quantity
 * @property \Illuminate\Support\Carbon|null $trial_ends_at
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Cashier\SubscriptionItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Team|null $owner
 * @property-read mixed $plan_data
 * @property-read \App\Models\Team|null $team
 * @property-read \App\Models\Team|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription canceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription ended()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription expiredTrial()
 * @method static \Database\Factories\SubscriptionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription incomplete()
 * @method static Builder<static>|Subscription newModelQuery()
 * @method static Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription notOnGracePeriod()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription notOnTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription onGracePeriod()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription onTrial()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription pastDue()
 * @method static Builder<static>|Subscription period(string $period)
 * @method static Builder<static>|Subscription plan(string $planName)
 * @method static Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription recurring()
 * @method static Builder<static>|Subscription search(string $search)
 * @method static Builder<static>|Subscription status(string $status)
 * @method static Builder<static>|Subscription whereCreatedAt($value)
 * @method static Builder<static>|Subscription whereEndsAt($value)
 * @method static Builder<static>|Subscription whereId($value)
 * @method static Builder<static>|Subscription whereQuantity($value)
 * @method static Builder<static>|Subscription whereStripeId($value)
 * @method static Builder<static>|Subscription whereStripePrice($value)
 * @method static Builder<static>|Subscription whereStripeStatus($value)
 * @method static Builder<static>|Subscription whereTeamId($value)
 * @method static Builder<static>|Subscription whereTrialEndsAt($value)
 * @method static Builder<static>|Subscription whereType($value)
 * @method static Builder<static>|Subscription whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Subscription extends CashierSubscription
{
    /**
     * @param  Builder<Subscription>  $query
     */
    #[Scope]
    protected function search(Builder $query, string $search): void
    {
        $query->whereHas('team.owner', function (Builder $q) use ($search): void {
            $q->where('email', 'like', "%{$search}%");
        });
    }

    /**
     * @param  Builder<Subscription>  $query
     */
    #[Scope]
    protected function status(Builder $query, string $status): void
    {
        $query->where('stripe_status', $status);
    }

    /**
     * @param  Builder<Subscription>  $query
     */
    #[Scope]
    protected function plan(Builder $query, string $planName): void
    {
        /** @var PlanService $planService */
        $planService = app(PlanService::class);

        $plan = $planService->all()->firstWhere('name.value', $planName);
        if ($plan) {
            $query->whereIn('stripe_price', array_values($plan->prices));
        }
    }

    /**
     * @param  Builder<Subscription>  $query
     */
    #[Scope]
    protected function period(Builder $query, string $period): void
    {
        /** @var PlanService $planService */
        $planService = app(PlanService::class);

        $priceIds = collect($planService->all())
            ->pluck('prices')
            ->pluck($period)
            ->filter()
            ->all();

        $query->whereIn('stripe_price', $priceIds);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * @return Attribute<PlanData, never>
     */
    protected function planData(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => app(PlanService::class)->findOrFailPlanByStripePriceId($attributes['stripe_price'])
        )->shouldCache();
    }

    protected static function newFactory(): SubscriptionFactory
    {
        return SubscriptionFactory::new();
    }
}
