<?php

declare(strict_types=1);

use App\Models\Subscription;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('denies access to non admin users', function (): void {
    $user = User::factory()->create(['is_admin' => false]);
    actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
});

it('allows access to admin users', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    actingAs($admin)->get(route('admin.dashboard'))->assertOk();
});

it('renders the correct inertia component', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertInertia(fn (Assert $page) => $page->component('admin/AdminDashboard'));
});

it('correctly displays performance statistics', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    Subscription::factory()->create(['created_at' => now()->subDays(5)]);
    Subscription::factory()->create(['created_at' => now()->subDays(10)]);
    Subscription::factory()->create(['created_at' => now()->subDays(40)]);

    actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->where('monthPerformance.newSubscriptions.value', 2)
                ->where('monthPerformance.newSubscriptions.variation', 100)
        );
});

it('correctly displays the current overview of subscriptions', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    Subscription::factory()->active()->count(3)->create();
    Subscription::factory()->trialing()->count(2)->create();
    Subscription::factory()->canceled()->count(1)->create();

    actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->where('currentOverview.activeSubscriptions', 6)
                ->where('currentOverview.subscriptionsOnTrial', 2)
                ->where('currentOverview.subscriptionsOnGracePeriod', 1)
        );
});
