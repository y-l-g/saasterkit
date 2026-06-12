<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use App\Notifications\TeamOwnershipTransferNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('allows the team owner to send an ownership transfer invitation', function (): void {
    Notification::fake();
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $member = User::factory()->create();
    $team->users()->syncWithPivotValues($member->id, ['role' => 'admin'], false);

    actingAs($owner)
        ->post(scoped_route('teams.ownership.invitations.send', $team), ['email' => $member->email])
        ->assertSessionHas('success');

    Notification::assertSentTo(
        $member,
        TeamOwnershipTransferNotification::class
    );
});

it('normalizes ownership transfer invitation emails', function (): void {
    Notification::fake();
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $member = User::factory()->create(['email' => 'member@example.com']);
    $team->users()->syncWithPivotValues($member->id, ['role' => 'admin'], false);

    actingAs($owner)
        ->post(scoped_route('teams.ownership.invitations.send', $team), ['email' => '  Member@Example.COM  '])
        ->assertSessionHas('success');

    expect($team->ownershipInvitations()->firstOrFail()->new_owner_email)->toBe('member@example.com');

    Notification::assertSentTo(
        $member,
        TeamOwnershipTransferNotification::class
    );
});

it('allows the recipient to accept the ownership transfer', function (): void {
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $member = User::factory()->create();
    $team->users()->syncWithPivotValues($member->id, ['role' => 'admin'], false);

    $invitation = $team->ownershipInvitations()->create([
        'new_owner_email' => $member->email,
        'token' => 'test-token',
    ]);

    $acceptUrl = URL::temporarySignedRoute('teams.ownership.invitations.mail.accept', now()->addDay(), ['token' => $invitation->token]);

    actingAs($member)
        ->get($acceptUrl)
        ->assertRedirect(scoped_route('dashboard', $team));

    $team->refresh();
    $owner->refresh();

    expect($team->user_id)->toBe($member->id);
    expect($owner->teamRole($team)->value)->toBe('admin');
});

it('allows the recipient to accept ownership transfer when email casing differs', function (): void {
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $member = User::factory()->create(['email' => 'member@example.com']);
    $team->users()->syncWithPivotValues($member->id, ['role' => 'admin'], false);

    $invitation = $team->ownershipInvitations()->create([
        'new_owner_email' => 'Member@Example.COM',
        'token' => 'test-token',
    ]);

    $acceptUrl = URL::temporarySignedRoute('teams.ownership.invitations.mail.accept', now()->addDay(), ['token' => $invitation->token]);

    actingAs($member)
        ->get($acceptUrl)
        ->assertRedirect(scoped_route('dashboard', $team));

    expect($team->fresh()->user_id)->toBe($member->id);
});

it('does not let an admin accept an ownership transfer sent to another email address', function (): void {
    $owner = User::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create(['email' => 'member@example.com']);
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $team->users()->syncWithPivotValues([$admin->id, $member->id], ['role' => 'admin'], false);

    $invitation = $team->ownershipInvitations()->create([
        'new_owner_email' => $member->email,
        'token' => 'test-token',
    ]);

    $acceptUrl = URL::temporarySignedRoute('teams.ownership.invitations.mail.accept', now()->addDay(), ['token' => $invitation->token]);

    actingAs($admin)->get($acceptUrl)->assertForbidden();

    expect($team->fresh()->user_id)->toBe($owner->id);
    expect($team->ownershipInvitations()->exists())->toBeTrue();
});
