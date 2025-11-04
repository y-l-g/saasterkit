<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('renders the users teams page', function (): void {
    $user = User::factory()->create();
    actingAs($user)->get(route('user.teams'))->assertOk();
});

it('displays a list of teams the user belongs to', function (): void {
    $user = User::factory()->has(Team::factory()->count(3), 'teams')->create();
    $user->current_team_id = $user->teams->first()->id;
    $user->save();

    actingAs($user)
        ->get(route('user.teams'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/UserTeamIndex')
                ->has('teams', 3)
        );
});
