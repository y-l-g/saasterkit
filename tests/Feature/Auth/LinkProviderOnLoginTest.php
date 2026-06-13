<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;

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

it('prompts for login and links account if email already exists', function (): void {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    mockSocialiteProvider(' Test@Example.COM ', 'Test User From Google', '12345');

    $response = get(route('provider.callback', 'google'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('info', 'An account with this email already exists. We have sent a login link to your email address.');
});
