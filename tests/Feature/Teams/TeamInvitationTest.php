<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
    $this->owner = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->owner->id]);
});

it('allows an authorized user to send a team invitation', function (): void {
    actingAs($this->owner)
        ->post(route('teams.members.store', $this->team), [
            'email' => 'new@member.com',
            'role' => 'editor',
        ])
        ->assertSessionHas('success');

    assertDatabaseHas('team_invitations', ['email' => 'new@member.com']);

    Notification::assertSentOnDemand(
        TeamInvitationNotification::class,
        function ($notification, $channels, $notifiable) {
            return $notifiable->routes['mail'] === 'new@member.com';
        }
    );
});


it('fails if the user is already a member of the team', function (): void {
    $member = User::factory()->create();
    $this->team->users()->sync($member, false);

    actingAs($this->owner)
        ->post(route('teams.members.store', $this->team), [
            'email' => $member->email,
            'role' => 'editor',
        ])
        ->assertSessionHasErrors('email');
});

it('allows an authorized user to cancel a pending invitation', function (): void {
    $invitation = TeamInvitation::factory()->create(['team_id' => $this->team->id]);

    actingAs($this->owner)
        ->delete(route('teams.invitations.destroy', $invitation))
        ->assertSessionHas('success');

    assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
});
