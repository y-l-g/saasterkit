<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('displays the password update page', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(scoped_route('password.edit', $team))
        ->assertOk();
});

it('allows the password to be updated', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->from(scoped_route('password.edit', $team))
        ->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(scoped_route('password.edit', $team));

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

it('requires the correct current password to update', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->from(scoped_route('password.edit', $team))
        ->put(route('user-password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasErrors(['current_password' => 'The provided password does not match your current password.'])
        ->assertRedirect(scoped_route('password.edit', $team));
});
