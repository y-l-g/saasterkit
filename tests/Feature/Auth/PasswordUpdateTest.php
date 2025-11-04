<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('displays the password update page', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('password.edit'))
        ->assertOk();
});

it('allows the password to be updated', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->from(route('password.edit'))
        ->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('password.edit'));

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

it('requires the correct current password to update', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->from(route('password.edit'))
        ->put(route('user-password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasErrors(['current_password' => 'The provided password does not match your current password.'])
        ->assertRedirect(route('password.edit'));
});
