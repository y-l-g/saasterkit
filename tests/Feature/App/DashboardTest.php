<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('redirects unauthenticated users to the login page', function (): void {
    get(route('dashboard'))->assertRedirect(route('login'));
});

it('redirects authenticated users without a team to the onboarding page', function (): void {
    $user = User::factory()->create(['current_team_id' => null]);

    actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('onboarding'));
});

it('successfully renders the dashboard for an authenticated user with a team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->current_team_id = $team->id;
    $user->save();

    actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});
