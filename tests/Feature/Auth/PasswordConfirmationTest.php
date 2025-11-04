<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can render the confirm password screen', function (): void {
    $user = User::factory()->create();

    actingAs($user)->get(route('password.confirm'))->assertOk()
        ->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('auth/ConfirmPassword')
        );
});

it('requires authentication for password confirmation screen', function (): void {
    get(route('password.confirm'))
        ->assertRedirect(route('login'));
});
