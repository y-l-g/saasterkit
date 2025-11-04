<?php

declare(strict_types=1);

use App\Models\Subscription;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->create(['is_admin' => true]);
    Subscription::factory()->count(20)->create();
});

it('denies access to non admin users', function (): void {
    $user = User::factory()->create(['is_admin' => false]);
    actingAs($user)->get(route('admin.subscriptions.index'))->assertForbidden();
});

it('allows access to admin users', function (): void {
    actingAs($this->admin)->get(route('admin.subscriptions.index'))->assertOk();
});

it('displays a paginated list of subscriptions', function (): void {
    actingAs($this->admin)
        ->get(route('admin.subscriptions.index'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('admin/AdminSubscriptionIndex')
                ->has('subscriptions.data', 10)
                ->has('subscriptions.next_page_url')
        );
});
