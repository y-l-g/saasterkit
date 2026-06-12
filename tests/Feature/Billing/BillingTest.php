<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use App\Services\PlanService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows a user to start a checkout session', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();
    $team->createAsStripeCustomer();

    /** @var PlanService $planService */
    $planService = resolve(PlanService::class);
    $plan = $planService->all()->first();
    $priceId = $plan->prices['month'];

    actingAs($user)
        ->get(scoped_route('billing.checkout', $team, ['stripePriceId' => $priceId]))
        ->assertRedirectContains('https://checkout.stripe.com');
});

it('allows a user to be redirected to the billing portal', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();
    $team->createAsStripeCustomer();

    actingAs($user)
        ->get(scoped_route('billing.portal', $team))
        ->assertRedirectContains('https://billing.stripe.com');
});
