<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

test('a user who owns a team cannot delete their account', function (): void {
    $user = User::factory()
        ->has(Team::factory(), 'ownedTeams')
        ->create();

    $this->actingAs($user);

    $response = $this->delete('settings/profile', [
        'password' => 'password',
    ]);

    expect($user->fresh())->not->toBeNull();
    $response->assertSessionHas('error', 'You must delete the teams you own before deleting your account');
});

test('a user who does not own any team can delete their account', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->delete('settings/profile', [
        'password' => 'password',
    ]);

    expect($user->fresh())->toBeNull();
});

test('correct password must be provided before an account can be deleted', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->delete('settings/profile', [
        'password' => 'wrong-password',
    ]);

    expect($user->fresh())->not->toBeNull();
});
