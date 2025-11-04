<?php

declare(strict_types=1);

use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('denies access if user does not have team delete permission', function (): void {
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $member = User::factory()->create();
    $team->users()->syncWithPivotValues($member->id, ['role' => 'editor'], false);

    actingAs($member)->delete(route('teams.destroy', $team))->assertForbidden();
});

it('fails if the user provides the wrong password', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->delete(route('teams.destroy', $team), ['password' => 'wrong-password'])
        ->assertSessionHasErrors('password');
});

it('fails if the team has an active subscription', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    Subscription::factory()->active()->create(['team_id' => $team->id]);

    actingAs($user)
        ->delete(route('teams.destroy', $team), ['password' => 'password'])
        ->assertSessionHas('error');
});

it('allows an authorized user to delete a team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->delete(route('teams.destroy', $team), ['password' => 'password'])
        ->assertRedirect(route('dashboard'));

    assertDatabaseMissing('teams', ['id' => $team->id]);
});
