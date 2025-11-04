<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Fortify\Features;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can render the login screen', function (): void {
    get(route('login'))->assertOk();
});

it('allows users to authenticate using the login screen', function (): void {
    $user = User::factory()->create();

    post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard', absolute: false));

    assertAuthenticated();
});

it('redirects users with two-factor enabled to the two-factor challenge', function (): void {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    $user = User::factory()->withTwoFactor()->create();

    $response = post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $response->assertSessionHas('login.id', $user->id);
    assertGuest();
});

it('does not authenticate with an invalid password', function (): void {
    $user = User::factory()->create();

    post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    assertGuest();
});

it('allows users to logout', function (): void {
    $user = User::factory()->create();

    $response = test()->actingAs($user)->post(route('logout'));

    assertGuest();
    $response->assertRedirect(route('home'));
});

it('rate limits login attempts', function (): void {
    $user = User::factory()->create();

    // Perform 5 failed login attempts to trigger rate limiting
    for ($i = 0; $i < 5; $i++) {
        post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    // The 6th attempt should be rate limited
    post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertTooManyRequests();
});
