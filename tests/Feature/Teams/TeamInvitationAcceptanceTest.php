<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

it('allows a logged in user to accept an invitation via a signed url', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $invitation = TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => $user->email]);

    $acceptUrl = URL::temporarySignedRoute('team.invitations.mail.accept', now()->addDay(), ['invitation' => $invitation]);

    actingAs($user)
        ->get($acceptUrl)
        ->assertRedirect(scoped_route('dashboard', $team));

    expect($user->belongsToTeam($team))->toBeTrue();
    assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
});

it('allows bulk invitation acceptance when email casing differs from the user email', function (): void {
    $user = User::factory()->create(['email' => 'member@example.com']);
    $team = Team::factory()->create();
    $invitation = TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => 'Member@Example.COM']);

    actingAs($user)
        ->post(route('teams.invitations.accept'), ['invitations' => [$invitation->id]])
        ->assertRedirect(scoped_route('dashboard', $team))
        ->assertSessionHas('success');

    expect($user->fresh()->belongsToTeam($team))->toBeTrue();
    assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
});

it('allows invitation acceptance when email casing differs from the user email', function (): void {
    $user = User::factory()->create(['email' => 'member@example.com']);
    $team = Team::factory()->create();
    $invitation = TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => 'Member@Example.COM']);
    $acceptUrl = URL::temporarySignedRoute('team.invitations.mail.accept', now()->addDay(), ['invitation' => $invitation]);

    actingAs($user)
        ->get($acceptUrl)
        ->assertRedirect(scoped_route('dashboard', $team));

    expect($user->belongsToTeam($team))->toBeTrue();
    assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
});

it('fails if a user tries to accept an invitation for another email address', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $invitation = TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => 'another@email.com']);
    $acceptUrl = URL::temporarySignedRoute('team.invitations.mail.accept', now()->addDay(), ['invitation' => $invitation]);

    actingAs($user)->get($acceptUrl)->assertForbidden();
});

it('does not let an admin accept an invitation sent to another email address', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $invitee = User::factory()->create(['email' => 'member@example.com']);
    $team = Team::factory()->create(['user_id' => $admin->id]);
    $invitation = TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => $invitee->email]);
    $acceptUrl = URL::temporarySignedRoute('team.invitations.mail.accept', now()->addDay(), ['invitation' => $invitation]);

    actingAs($admin)->get($acceptUrl)->assertForbidden();

    expect($admin->fresh()->belongsToTeam($team))->toBeTrue();
    expect($invitee->fresh()->belongsToTeam($team))->toBeFalse();
    assertDatabaseHas('team_invitations', ['id' => $invitation->id]);
});

it('rejects expired signed invitation links', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $invitation = TeamInvitation::factory()->create(['team_id' => $team->id, 'email' => $user->email]);
    $acceptUrl = URL::temporarySignedRoute('team.invitations.mail.accept', now()->subMinute(), ['invitation' => $invitation]);

    actingAs($user)->get($acceptUrl)->assertForbidden();

    expect($user->fresh()->belongsToTeam($team))->toBeFalse();
    assertDatabaseHas('team_invitations', ['id' => $invitation->id]);
});
