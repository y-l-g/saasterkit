<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('displays the profile page', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk();
});

it('allows the user to update their profile information', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->from(route('profile.edit')) // Specify the origin of the request
        ->put(route('user-profile-information.update'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

it('keeps the email verification status when the email is unchanged', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->from(route('profile.edit')) // Specify the origin of the request
        ->put(route('user-profile-information.update'), [
            'name' => 'Test User',
            'email' => $user->email,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});
