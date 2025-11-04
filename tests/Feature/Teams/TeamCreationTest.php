<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('allows an authenticated user to create a new team', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('teams.store'), ['name' => 'My New Team'])
        ->assertRedirect();

    $user->refresh();

    expect($user->ownedTeams)->toHaveCount(1);
    expect($user->ownedTeams->first()->name)->toBe('My New Team');
    expect($user->current_team_id)->toBe($user->ownedTeams->first()->id);
});

it('validates that the team name is required', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('teams.store'), ['name' => ''])
        ->assertSessionHasErrors('name');
});

it('redirects the user to the new teams billing page after creation', function (): void {
    $user = User::factory()->create();

    $response = actingAs($user)
        ->post(route('teams.store'), ['name' => 'My New Team']);

    $team = $user->fresh()->ownedTeams->first();
    $response->assertRedirect(route('billing.show', $team));
});
