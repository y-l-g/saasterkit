<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it clears cache for old and new owner when team owner changes', function (): void {
    $oldOwner = User::factory()->create();
    $newOwner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $oldOwner->id]);

    $oldOwnerCacheKey = "user.{$oldOwner->id}.team.{$team->id}.permissions";
    $newOwnerCacheKey = "user.{$newOwner->id}.team.{$team->id}.permissions";

    Cache::put($oldOwnerCacheKey, ['stale-permission-for-old-owner']);
    Cache::put($newOwnerCacheKey, ['stale-permission-for-new-owner']);

    expect(Cache::has($oldOwnerCacheKey))->toBeTrue();
    expect(Cache::has($newOwnerCacheKey))->toBeTrue();

    $team->user_id = $newOwner->id;
    $team->save();

    expect(Cache::has($oldOwnerCacheKey))->toBeFalse();
    expect(Cache::has($newOwnerCacheKey))->toBeFalse();
});

test('it does not clear cache when a non-owner field is updated', function (): void {
    $owner = User::factory()->create();
    $team = Team::factory()->create(['user_id' => $owner->id]);
    $cacheKey = "user.{$owner->id}.team.{$team->id}.permissions";

    Cache::put($cacheKey, ['some-valid-permissions']);
    expect(Cache::has($cacheKey))->toBeTrue();

    $team->name = 'New Team Name';
    $team->save();

    expect(Cache::has($cacheKey))->toBeTrue();
});
