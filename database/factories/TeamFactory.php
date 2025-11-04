<?php

namespace Database\Factories;

use App\Enums\Billing\PlanEnum;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'user_id' => User::factory(),
            'created_at' => $this->faker->dateTimeThisYear(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Team $team) {
            $team->users()->sync($team->owner, false);
            $team->owner->forceFill([
                'current_team_id' => $team->id,
            ])->save();
        });
    }

    public function withSubscription(string $status = 'active', ?string $plan = null, ?string $interval = null): static
    {
        return $this->has(
            Subscription::factory()
                ->state(function (array $attributes, Team $team) use ($plan, $interval): array {
                    $subscriptionData = [
                        'created_at' => fake()->dateTimeBetween($team->created_at, 'now'),
                    ];

                    if ($plan && $interval) {
                        $subscriptionData['stripe_price'] = PlanEnum::from($plan)->prices()[$interval];
                    }

                    return $subscriptionData;
                })
                ->{$status}()
        );
    }
}
