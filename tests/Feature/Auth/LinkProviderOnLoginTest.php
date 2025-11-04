<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\get;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('prompts for login and links account if email already exists', function (): void {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    mockSocialiteProvider('test@example.com', 'Test User From Google', '12345');

    $response = get(route('provider.callback', 'google'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('info', 'An account with this email already exists. We have sent a login link to your email address.');
});
