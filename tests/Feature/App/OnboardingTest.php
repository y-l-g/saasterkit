<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

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
    TeamInvitation::factory()->create(['accepted_at' => now(), 'team_id' => $team->id, 'email' => $user->email]);
    TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => 'another@email.com']);

    actingAs($user)
        ->get(route('onboarding'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->has('invitations', 1)
                ->where('invitations.0.email', $user->email)
        );
});

it('displays pending team invitations when email casing differs', function (): void {
    $user = User::factory()->create(['email' => 'member@example.com']);
    $team = Team::factory()->create();
    TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => 'Member@Example.COM']);

    actingAs($user)
        ->get(route('onboarding'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->has('invitations', 1)
                ->where('invitations.0.email', 'Member@Example.COM')
        );
});

it('displays an empty state when there are no pending invitations', function (): void {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('onboarding'))
        ->assertInertia(fn (Assert $page) => $page->has('invitations', 0));
});
