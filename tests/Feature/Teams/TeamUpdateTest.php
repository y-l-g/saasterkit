<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('denies access if user does not have team update permission', function (): void {
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $member = User::factory()->create();
    $team->users()->sync($member->id, ['role' => 'editor'], false);

    actingAs($member)
        ->put(route('teams.update', $team), ['name' => 'New Team Name'])
        ->assertForbidden();
});

it('allows an authorized user to update the teams name', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->put(route('teams.update', $team), ['name' => 'New Team Name'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($team->fresh()->name)->toBe('New Team Name');
});

it('fails validation if the name is empty', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->put(route('teams.update', $team), ['name' => ''])
        ->assertSessionHasErrors('name');
});
