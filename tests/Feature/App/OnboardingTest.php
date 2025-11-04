<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('successfully renders the onboarding page', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('onboarding'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('app/Onboarding'));
});

it('displays pending team invitations for the logged in users email', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => $user->email]);
    TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => 'another@email.com']);

    actingAs($user)
        ->get(route('onboarding'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->has('invitations', 1)
                ->where('invitations.0.email', $user->email)
        );
});

it('displays an empty state when there are no pending invitations', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('onboarding'))
        ->assertInertia(fn (Assert $page) => $page->has('invitations', 0));
});
