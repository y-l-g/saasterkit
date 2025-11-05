<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use App\Notifications\TeamOwnershipTransferNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('allows the team owner to send an ownership transfer invitation', function (): void {
    Notification::fake();
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $member = User::factory()->create();
    $team->users()->syncWithPivotValues($member->id, ['role' => 'admin'], false);

    actingAs($owner)
        ->post(route('teams.ownership.invitations.send', $team), ['email' => $member->email])
        ->assertSessionHas('success');

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
        ->assertRedirect(route('dashboard'));

    $team->refresh();
    $owner->refresh();

    expect($team->user_id)->toBe($member->id);
    expect($owner->teamRole($team)->value)->toBe('admin');
});
