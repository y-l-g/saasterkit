<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('allows a logged in user to accept an invitation via a signed url', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $invitation = TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => $user->email]);

    $acceptUrl = URL::signedRoute('team.invitations.mail.accept', ['invitation' => $invitation]);

    actingAs($user)
        ->get($acceptUrl)
        ->assertRedirect(route('dashboard'));

    expect($user->belongsToTeam($team))->toBeTrue();
    assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
});

it('fails if a user tries to accept an invitation for another email address', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $invitation = TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => 'another@email.com']);
    $acceptUrl = URL::signedRoute('team.invitations.mail.accept', ['invitation' => $invitation]);

    actingAs($user)->get($acceptUrl)->assertForbidden();
});
