<?php

declare(strict_types=1);

use App\Models\SocialAccount;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function mockSocialiteProvider(string $email = 'test@example.com', string $name = 'Test User', string $id = '12345'): void
{
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->allows('getId')->andReturn($id);
    $socialiteUser->allows('getName')->andReturn($name);
    $socialiteUser->allows('getEmail')->andReturn($email);
    $socialiteUser->token = 'test-token';
    $socialiteUser->refreshToken = 'test-refresh-token';

    $provider = Mockery::mock(GoogleProvider::class);
    $provider->allows('stateless->user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
}

it('can register and authenticate a new user via socialite', function (): void {
    mockSocialiteProvider('test@example.com', 'Test User');

    get(route('provider.callback', 'google'));

    assertDatabaseHas('users', ['email' => 'test@example.com']);
    assertAuthenticated();
});

it('can authenticate an existing user via socialite', function (): void {
    $user = User::factory()
        ->has(SocialAccount::factory(['provider' => 'google', 'provider_id' => '12345']))
        ->create();

    mockSocialiteProvider($user->email, $user->name, '12345');

    get(route('provider.callback', 'google'));

    assertAuthenticatedAs($user);
});
