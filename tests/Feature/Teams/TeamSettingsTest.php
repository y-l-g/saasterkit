<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('denies access if user is not part of the team', function (): void {
    $user = User::factory()->create();
    $user_team = Team::factory()->create(['user_id' => $user->id]);
    $user->switchToTeam($user_team);

    $other_team = Team::factory()->create();

    actingAs($user)->get(scoped_route('teams.settings.show', $other_team))->assertForbidden();
});

it('renders the team settings page', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->teams()->sync($team->id, false);

    actingAs($user)->get(scoped_route('teams.settings.show', $team))->assertOk();
});

it('only displays pending sent invitations', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->teams()->sync($team->id, false);
    TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => 'pending@example.com']);
    TeamInvitation::factory()->create(['accepted_at' => now(), 'team_id' => $team->id, 'email' => 'accepted@example.com']);

    actingAs($user)
        ->get(scoped_route('teams.settings.show', $team))
        ->assertInertia(
            fn (Assert $page) => $page
                ->has('team.invitations', 1)
                ->where('team.invitations.0.email', 'pending@example.com')
        );
});
