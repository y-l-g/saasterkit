<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('renders the profile settings page', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)->get(scoped_route('profile.edit', $team))->assertOk();
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
