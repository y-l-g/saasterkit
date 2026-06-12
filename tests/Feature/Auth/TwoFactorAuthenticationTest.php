<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Fortify\Features;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('can render the two factor settings page', function (): void {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(scoped_route('two-factor.show', $team))
        ->assertInertia(
            fn (Assert $page): Assert => $page
                ->component('settings/TwoFactor')
                ->where('twoFactorEnabled', false)
        );
});

it('requires password confirmation for two factor settings page when enabled', function (): void {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    $user = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $user->id]);

    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]);

    actingAs($user)
        ->get(scoped_route('two-factor.show', $team))
        ->assertRedirect(route('password.confirm'));
});
