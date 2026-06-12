<?php

declare(strict_types=1);

use App\Models\SocialAccount;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('displays the profile page', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(scoped_route('profile.edit', $team))
        ->assertOk();
});

it('allows the user to update their profile information', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->from(scoped_route('profile.edit', $team))
        ->put(route('user-profile-information.update'), [
            'name' => 'Test User',
            'email' => ' Test@Example.COM ',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(scoped_route('profile.edit', $team));

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

it('keeps the email verification status when only email casing and spaces change', function (): void {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->from(scoped_route('profile.edit', $team))
        ->put(route('user-profile-information.update'), [
            'name' => 'Test User',
            'email' => ' Test@Example.COM ',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(scoped_route('profile.edit', $team));

    $user->refresh();

    expect($user->email)->toBe('test@example.com');
    expect($user->email_verified_at)->not->toBeNull();
});

it('keeps the email verification status when the email is unchanged', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->from(scoped_route('profile.edit', $team))
        ->put(route('user-profile-information.update'), [
            'name' => 'Test User',
            'email' => $user->email,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(scoped_route('profile.edit', $team));

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

it('does not allow email changes while social accounts are linked', function (): void {
    $user = User::factory()->create(['email' => 'user@example.com']);
    $team = Team::factory()->create(['user_id' => $user->id]);
    SocialAccount::factory()->for($user)->create();

    actingAs($user)
        ->from(scoped_route('profile.edit', $team))
        ->put(route('user-profile-information.update'), [
            'name' => 'Test User',
            'email' => 'new@example.com',
        ])
        ->assertSessionHasErrors('email')
        ->assertRedirect(scoped_route('profile.edit', $team));

    $user->refresh();

    expect($user->email)->toBe('user@example.com');
    expect($user->email_verified_at)->not->toBeNull();
});
