<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use App\Services\PlanService;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('allows a user to start a checkout session', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();
    $team->createAsStripeCustomer();

    /** @var PlanService $planService */
    $planService = app(PlanService::class);
    $plan = $planService->all()->first();
    $priceId = $plan->prices['month'];

    actingAs($user)
        ->get(route('billing.checkout', ['stripePriceId' => $priceId, 'team' => $team]))
        ->assertRedirectContains('https://checkout.stripe.com');
});

it('allows a user to be redirected to the billing portal', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();
    $team->createAsStripeCustomer();

    actingAs($user)
        ->get(route('billing.portal', ['team' => $team]))
        ->assertRedirectContains('https://billing.stripe.com');
});
