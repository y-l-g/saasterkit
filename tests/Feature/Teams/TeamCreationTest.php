<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows an authenticated user to create a new team', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('teams.store'), ['name' => 'My New Team'])
        ->assertRedirect();

    $user->refresh();

    expect($user->ownedTeams)->toHaveCount(1);
    expect($user->ownedTeams->first()->name)->toBe('My New Team');
    expect($user->ownedTeams->first()->slug)->toBe('my-new-team');
    expect($user->current_team_id)->toBe($user->ownedTeams->first()->id);
});

it('generates unique team slugs', function (): void {
    $firstTeam = Team::factory()->create(['name' => 'Acme Studio']);
    $secondTeam = Team::factory()->create(['name' => 'Acme Studio']);

    expect($firstTeam->slug)->toBe('acme-studio');
    expect($secondTeam->slug)->toBe('acme-studio-1');
});

it('updates the team slug when the team name changes', function (): void {
    $team = Team::factory()->create(['name' => 'Original Studio']);

    $team->forceFill(['name' => 'Updated Studio'])->save();

    expect($team->refresh()->slug)->toBe('updated-studio');
});

it('uses the team slug for route model binding URLs', function (): void {
    $team = Team::factory()->create(['name' => 'Slugged Team']);

    expect(route('teams.settings.show', $team, absolute: false))->toBe('/teams/slugged-team');
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
