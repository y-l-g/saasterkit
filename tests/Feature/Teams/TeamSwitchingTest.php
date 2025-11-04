<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('allows a user to switch their active team', function (): void {
    $user = User::factory()->create();
    $team1 = Team::factory()->create(['user_id' => $user->id]);
    $team2 = Team::factory()->create(['user_id' => $user->id]);
    $user->teams()->sync([$team1->id, $team2->id], false);
    $user->switchToTeam($team1);

    actingAs($user)
        ->put(route('teams.current.update'), ['team_id' => $team2->id])
        ->assertRedirect(route('dashboard'));

    expect($user->fresh()->current_team_id)->toBe($team2->id);
});

it('fails if a user tries to switch to a team they do not belong to', function (): void {
    $user = User::factory()->create();
    $otherTeam = Team::factory()->create();

    actingAs($user)
        ->put(route('teams.current.update'), ['team_id' => $otherTeam->id])
        ->assertForbidden();
});
