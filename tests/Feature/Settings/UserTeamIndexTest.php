<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('renders the users teams page', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)->get(scoped_route('user.teams', $team))->assertOk();
});

it('displays a list of teams the user belongs to', function (): void {
    $user = User::factory()->has(Team::factory()->count(3), 'teams')->create();
    $user->current_team_id = $user->teams->first()->id;
    $user->save();

    actingAs($user)
        ->get(scoped_route('user.teams', $user->teams->first()))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/UserTeamIndex')
                ->has('teams', 3)
        );
});

it('only displays pending invitations for the logged in users email', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => $user->email]);
    TeamInvitation::factory()->create(['accepted_at' => now(), 'team_id' => $team->id, 'email' => $user->email]);

    actingAs($user)
        ->get(scoped_route('user.teams', $team))
        ->assertInertia(
            fn (Assert $page) => $page
                ->has('invitations', 1)
                ->where('invitations.0.email', $user->email)
        );
});
