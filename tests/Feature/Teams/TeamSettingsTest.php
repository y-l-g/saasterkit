<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('denies access if user is not part of the team', function (): void {
    $user = User::factory()->create();
    $user_team = Team::factory()->create(['user_id' => $user->id]);
    $user->switchToTeam($user_team);

    $other_team = Team::factory()->create();

    actingAs($user)->get(route('teams.settings.show', $other_team))->assertForbidden();
});

it('renders the team settings page', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->teams()->sync($team->id, false);

    actingAs($user)->get(route('teams.settings.show', $team))->assertOk();
});
