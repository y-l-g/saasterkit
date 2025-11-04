<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('renders the password settings page', function (): void {
    $user = User::factory()->create();
    actingAs($user)->get(route('password.edit'))->assertOk();
});

it('allows a user to update their password', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHas('status', 'password-updated');

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

it('fails to update password with incorrect current password', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->put(route('user-password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasErrors('current_password');
});

it('allows a user without a password to set one', function (): void {
    $user = User::factory()->create(['password' => null]);

    actingAs($user)
        ->put(route('user-password.update'), [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHas('status', 'password-updated');

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});
