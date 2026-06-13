<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Features;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('logs in a user without two-factor authentication and redirects to their team profile', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    $response = get(signedLoginLinkFor($user));

    $response->assertRedirect(scoped_route('profile.edit', $team));
    $response->assertSessionHas('success', 'You have been logged in successfully.');
    assertAuthenticated();
});

it('challenges a user with two-factor authentication instead of logging them in', function (): void {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    $user = User::factory()->withTwoFactor()->create();

    $response = get(signedLoginLinkFor($user));

    $response->assertRedirect(route('two-factor.login'));
    $response->assertSessionHas('login.id', $user->id);
    $response->assertSessionHas('login.remember', false);
    assertGuest();
});

it('redirects a logged in user without a team to onboarding', function (): void {
    $user = User::factory()->create(['current_team_id' => null]);

    $response = get(signedLoginLinkFor($user));

    $response->assertRedirect(route('onboarding'));
    $response->assertSessionHas('success', 'You have been logged in successfully.');
    assertAuthenticated();
});

it('rejects an unsigned login link', function (): void {
    $user = User::factory()->create();

    get(route('auth.login.link', ['user' => $user->id]))
        ->assertForbidden();

    assertGuest();
});

it('rejects an expired login link', function (): void {
    $user = User::factory()->create();

    $expiredUrl = URL::temporarySignedRoute(
        'auth.login.link',
        now()->subMinute(),
        ['user' => $user->id]
    );

    get($expiredUrl)
        ->assertForbidden();

    assertGuest();
});

function signedLoginLinkFor(User $user): string
{
    return URL::temporarySignedRoute(
        'auth.login.link',
        now()->addMinutes(5),
        ['user' => $user->id]
    );
}
