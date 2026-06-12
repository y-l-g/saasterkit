<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Notification::fake();
    $this->owner = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->owner->id]);
});

it('allows an authorized user to send a team invitation', function (): void {
    actingAs($this->owner)
        ->post(scoped_route('teams.members.store', $this->team), [
            'email' => '  New@Member.COM  ',
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
        ->post(scoped_route('teams.members.store', $this->team), [
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

it('does not allow accepted invitations to be canceled', function (): void {
    $invitation = TeamInvitation::factory()->create([
        'accepted_at' => now(),
        'team_id' => $this->team->id,
    ]);

    actingAs($this->owner)
        ->delete(route('teams.invitations.destroy', $invitation))
        ->assertSessionHasErrors('invitation');

    assertDatabaseHas('team_invitations', ['id' => $invitation->id]);
});

it('allows a new invitation after an earlier one was accepted', function (): void {
    TeamInvitation::factory()->create([
        'accepted_at' => now(),
        'team_id' => $this->team->id,
        'email' => 'member@example.com',
    ]);

    actingAs($this->owner)
        ->post(scoped_route('teams.members.store', $this->team), [
            'email' => 'member@example.com',
            'role' => 'editor',
        ])
        ->assertSessionHas('success');

    expect(TeamInvitation::query()
        ->where('team_id', $this->team->id)
        ->where('email', 'member@example.com')
        ->count())->toBe(2);
});

it('sends team invitation emails with expiring signed links', function (): void {
    $invitation = TeamInvitation::factory()->create(['team_id' => $this->team->id]);

    $mail = (new TeamInvitationNotification($invitation))->toMail($this->owner);

    expect(parse_url($mail->actionUrl, PHP_URL_QUERY))->toContain('expires=');
});
