<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('denies access if user does not have team update permission', function (): void {
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $member = User::factory()->create();
    $team->users()->sync($member->id, ['role' => 'editor'], false);

    actingAs($member)
        ->put(scoped_route('teams.update', $team), ['name' => 'New Team Name'])
        ->assertForbidden();
});

it('denies route team access before admin gate bypasses permissions', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    Team::factory()->create(['user_id' => $admin->id]);
    $team = Team::factory()->create();

    actingAs($admin)
        ->put(scoped_route('teams.update', $team), ['name' => 'New Team Name'])
        ->assertForbidden();
});

it('switches the current team to the route-bound team', function (): void {
    $user = User::factory()->create();
    $currentTeam = Team::factory()->create(['user_id' => $user->id]);
    $routeTeam = Team::factory()->create(['user_id' => $user->id]);

    $user->switchToTeam($currentTeam);

    actingAs($user)
        ->put(scoped_route('teams.update', $routeTeam), ['name' => 'Route Team Name'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->refresh()->current_team_id)->toBe($routeTeam->id);
});

it('allows an authorized user to update the teams name', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->put(scoped_route('teams.update', $team), ['name' => 'New Team Name'])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($team->fresh()->name)->toBe('New Team Name');
});

it('fails validation if the name is empty', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->put(scoped_route('teams.update', $team), ['name' => ''])
        ->assertSessionHasErrors('name');
});

it('fails validation if the updated name is reserved', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create([
        'name' => 'Original Team',
        'user_id' => $user->id,
    ]);

    actingAs($user)
        ->put(scoped_route('teams.update', $team), ['name' => 'Billing'])
        ->assertSessionHasErrors('name');

    $team->refresh();

    expect($team->name)->toBe('Original Team');
    expect($team->slug)->toBe('original-team');
});
