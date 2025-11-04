<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('renders the profile settings page', function (): void {
    $user = User::factory()->create();
    actingAs($user)->get(route('profile.edit'))->assertOk();
});

it('allows a user to update their name and email', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->put(route('user-profile-information.update'), [
            'name' => 'New Name',
            'email' => 'new@email.com',
        ])
        ->assertSessionHas('status', 'profile-information-updated');

    $user->refresh();
    expect($user->name)->toBe('New Name');
    expect($user->email)->toBe('new@email.com');
});

it('requires email verification when the email address is changed', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->put(route('user-profile-information.update'), [
            'name' => 'New Name',
            'email' => 'new@email.com',
        ]);

    expect($user->fresh()->email_verified_at)->toBeNull();
});
