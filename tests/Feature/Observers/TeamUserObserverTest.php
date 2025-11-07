<?php

declare(strict_types=1);

use App\Enums\Teams\RoleEnum;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it clears permissions cache when a user is added to a team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $cacheKey = "user.{$user->id}.team.{$team->id}.permissions";

    Cache::put($cacheKey, ['some-stale-data']);
    expect(Cache::has($cacheKey))->toBeTrue();

    $team->users()->attach($user, ['role' => RoleEnum::EDITOR]);

    expect(Cache::has($cacheKey))->toBeFalse();
});

test('it clears permissions cache when a user role is updated in a team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($user, ['role' => RoleEnum::EDITOR]);
    $cacheKey = "user.{$user->id}.team.{$team->id}.permissions";

    Cache::put($cacheKey, ['some-stale-data']);
    expect(Cache::has($cacheKey))->toBeTrue();

    $team->users()->updateExistingPivot($user->id, ['role' => RoleEnum::ADMIN]);

    expect(Cache::has($cacheKey))->toBeFalse();
});

test('it clears permissions cache when a user is removed from a team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($user, ['role' => RoleEnum::EDITOR]);
    $cacheKey = "user.{$user->id}.team.{$team->id}.permissions";

    Cache::put($cacheKey, ['some-stale-data']);
    expect(Cache::has($cacheKey))->toBeTrue();

    $team->users()->detach($user);

    expect(Cache::has($cacheKey))->toBeFalse();
});
