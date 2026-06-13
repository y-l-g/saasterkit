<?php

declare(strict_types=1);

use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\SubscriptionItem;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

it('denies access if user does not have team delete permission', function (): void {
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $member = User::factory()->create();
    $team->users()->syncWithPivotValues($member->id, ['role' => 'editor'], false);

    actingAs($member)->delete(scoped_route('teams.destroy', $team))->assertForbidden();
});

it('fails if the user provides the wrong password', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->delete(scoped_route('teams.destroy', $team), ['password' => 'wrong-password'])
        ->assertSessionHasErrors('password');
});

it('fails if the team has an active subscription', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    Subscription::factory()->active()->create(['team_id' => $team->id]);

    actingAs($user)
        ->delete(scoped_route('teams.destroy', $team), ['password' => 'password'])
        ->assertSessionHasErrors('subscription');
});

it('fails if the team has a subscription on grace period', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    Subscription::factory()->canceled()->create(['team_id' => $team->id]);

    actingAs($user)
        ->delete(scoped_route('teams.destroy', $team), ['password' => 'password'])
        ->assertSessionHasErrors('subscription');
});

it('allows deleting a team after its subscription has ended', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    Subscription::factory()->canceled()->create([
        'team_id' => $team->id,
        'ends_at' => now()->subDay(),
    ]);

    actingAs($user)
        ->delete(scoped_route('teams.destroy', $team), ['password' => 'password'])
        ->assertRedirect(route('onboarding'));

    assertDatabaseMissing('teams', ['id' => $team->id]);
});

it('removes ended subscription data when deleting a team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $subscription = Subscription::factory()->canceled()->create([
        'team_id' => $team->id,
        'ends_at' => now()->subDay(),
    ]);
    $subscriptionItem = SubscriptionItem::query()->create([
        'subscription_id' => $subscription->id,
        'stripe_id' => 'si_ended_subscription',
        'stripe_product' => 'prod_ended_subscription',
        'stripe_price' => $subscription->stripe_price,
        'quantity' => 1,
    ]);

    actingAs($user)
        ->delete(scoped_route('teams.destroy', $team), ['password' => 'password'])
        ->assertRedirect(route('onboarding'));

    assertDatabaseMissing('teams', ['id' => $team->id]);
    assertDatabaseMissing('subscriptions', ['id' => $subscription->id]);
    assertDatabaseMissing('subscription_items', ['id' => $subscriptionItem->id]);
});

it('allows an authorized user to delete a team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->delete(scoped_route('teams.destroy', $team), ['password' => 'password'])
        ->assertRedirect(route('onboarding'));

    assertDatabaseMissing('teams', ['id' => $team->id]);
});
