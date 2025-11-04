<?php

namespace Database\Factories;

use App\Enums\Billing\PlanEnum;
use App\Enums\Billing\SubscriptionStatusEnum;
use App\Models\Subscription;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'type' => 'default',
            'stripe_id' => 'sub_'.Str::random(40),
            'stripe_price' => PlanEnum::cases()[rand(0, 1)]->prices()[['month', 'year'][rand(0, 1)]],
            'stripe_status' => SubscriptionStatusEnum::STATUS_ACTIVE,
            'created_at' => fake()->dateTimeThisYear(),
            'updated_at' => now(),
            'trial_ends_at' => null,
            'ends_at' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'stripe_status' => 'active',
            'trial_ends_at' => null,
            'ends_at' => null,
        ]);
    }

    public function trialing(): static
    {
        return $this->state(fn (array $attributes) => [
            'stripe_status' => 'trialing',
            'trial_ends_at' => now()->addDays(14),
        ]);
    }

    public function canceled(): static
    {
        return $this->state(fn (array $attributes) => [
            'stripe_status' => 'canceled',
            'ends_at' => now()->addMonth(),
        ]);
    }
}
