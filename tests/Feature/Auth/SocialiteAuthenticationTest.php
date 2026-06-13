<?php

declare(strict_types=1);

use App\Models\SocialAccount;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

if (! function_exists('mockSocialiteProvider')) {
    function mockSocialiteProvider(string $email = 'test@example.com', string $name = 'Test User', string $id = '12345'): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->allows('getId')->andReturn($id);
        $socialiteUser->allows('getName')->andReturn($name);
        $socialiteUser->allows('getEmail')->andReturn($email);
        $socialiteUser->token = 'test-token';
        $socialiteUser->refreshToken = 'test-refresh-token';

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->allows('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }
}

it('can register and authenticate a new user via socialite', function (): void {
    mockSocialiteProvider(' Test@Example.COM ', 'Test User');

    get(route('provider.callback', 'google'));

    assertDatabaseHas('users', ['email' => 'test@example.com']);
    assertAuthenticated();
});

it('can link an authenticated user when provider email casing differs', function (): void {
    $user = User::factory()->create(['email' => 'test@example.com']);
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->switchToTeam($team);

    mockSocialiteProvider(' Test@Example.COM ', 'Test User', '67890');

    $this->actingAs($user)
        ->get(route('provider.callback', 'google'))
        ->assertRedirect(scoped_route('profile.edit', $team));

    assertDatabaseHas('social_accounts', [
        'user_id' => $user->id,
        'provider' => 'google',
        'provider_id' => '67890',
    ]);
});

it('redirects to the current team profile after unlinking a provider', function (): void {
    $user = User::factory()
        ->has(SocialAccount::factory(['provider' => 'google']))
        ->create();
    $team = Team::factory()->create(['user_id' => $user->id]);
    $user->switchToTeam($team);

    $this->actingAs($user)
        ->delete(route('provider.unlink', 'google'))
        ->assertRedirect(scoped_route('profile.edit', $team));

    $this->assertDatabaseMissing('social_accounts', [
        'user_id' => $user->id,
        'provider' => 'google',
    ]);
});

it('can authenticate an existing user via socialite', function (): void {
    $user = User::factory()
        ->has(SocialAccount::factory(['provider' => 'google', 'provider_id' => '12345']))
        ->create();

    mockSocialiteProvider($user->email, $user->name, '12345');

    get(route('provider.callback', 'google'));

    assertAuthenticatedAs($user);
});
