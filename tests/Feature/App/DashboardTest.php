<?php

declare(strict_types=1);

use App\Enums\Teams\RoleEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('redirects unauthenticated users to the login page', function (): void {
    get('/dashboard')->assertRedirect(route('login'));
});

it('redirects authenticated users without a team to the onboarding page', function (): void {
    $user = User::factory()->create(['current_team_id' => null]);

    actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('onboarding'));
});

it('successfully renders the dashboard for an authenticated user with a team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();

    actingAs($user)
        ->get(scoped_route('dashboard', $team))
        ->assertOk();
});

it('syncs the current team from the route team', function (): void {
    $user = User::factory()->create();
    $currentTeam = Team::factory()->create(['user_id' => $user->id]);
    $routeTeam = Team::factory()->create();

    $user->forceFill(['current_team_id' => $currentTeam->id])->save();
    $routeTeam->users()->attach($user, ['role' => RoleEnum::EDITOR]);

    actingAs($user)
        ->get(scoped_route('dashboard', $routeTeam))
        ->assertOk();

    expect($user->refresh()->current_team_id)->toBe($routeTeam->id);
});
