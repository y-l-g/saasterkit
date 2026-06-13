<?php

declare(strict_types=1);

use App\Enums\Teams\RoleEnum;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->user->teams()->sync($this->team->id, false);
    $this->user->current_team_id = $this->team->id;
    $this->user->save();
});

it('denies access if user is not team owner', function (): void {
    $member = User::factory()->create();
    $this->team->users()->syncWithPivotValues($member->id, ['role' => 'editor'], false);

    actingAs($member)->get(scoped_route('billing.show', $this->team))->assertForbidden();
});

it('renders the billing settings page for a team owner', function (): void {
    actingAs($this->user)
        ->get(scoped_route('billing.show', $this->team))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/Billing')
                ->missing('invoices')
                ->loadDeferredProps(fn (Assert $reload) => $reload->has('invoices', 0))
        );
});

it('renders the billing settings page for an admin team member', function (): void {
    $admin = User::factory()->create();
    $this->team->users()->syncWithPivotValues($admin->id, ['role' => RoleEnum::ADMIN->value], false);
    $admin->switchToTeam($this->team);

    actingAs($admin)
        ->get(scoped_route('billing.show', $this->team))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/Billing')
                ->missing('invoices')
        );
});

it('shows ended subscriptions as inactive billing state', function (): void {
    Subscription::factory()->canceled()->create([
        'team_id' => $this->team->id,
        'ends_at' => now()->subDay(),
    ]);

    actingAs($this->user)
        ->get(scoped_route('billing.show', $this->team))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->where('team.subscription.status', 'canceled')
                ->where('team.subscription.active', false)
                ->where('team.subscription.valid', false)
        );
});
