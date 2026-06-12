<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('sends a verification notification', function (): void {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect(route('home'));

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('does not send a verification notification if email is already verified', function (): void {
    Notification::fake();
    $user = User::factory()->verified()->create();

    actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect('/dashboard');

    Notification::assertNothingSent();
});
