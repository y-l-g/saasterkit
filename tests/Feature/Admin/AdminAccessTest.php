<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('denies access to the admin dashboard for non-admin users', function (): void {
    $user = User::factory()->create(['is_admin' => false]);

    actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('allows access to the admin dashboard for admin users', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();
});
