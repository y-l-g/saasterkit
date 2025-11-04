<?php

declare(strict_types=1);

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\actingAs;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('allows a user to dismiss a notification', function (): void {
    $user = User::factory()->create();
    $notification = AppNotification::factory()->create();
    $user->notifications()->attach($notification->id);

    actingAs($user)
        ->post(route('notifications.dismiss', $notification))
        ->assertRedirect()
        ->assertSessionHas('success', 'Notification dismissed.');

    $pivot = DB::table('app_notification_user')
        ->where('user_id', $user->id)
        ->where('app_notification_id', $notification->id)
        ->first();

    expect($pivot->dismissed_at)->not->toBeNull();
});

it('prevents a user from dismissing a notification they have not received', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $notification = AppNotification::factory()->create();
    $otherUser->notifications()->attach($notification->id);

    actingAs($user)
        ->post(route('notifications.dismiss', $notification));

    $pivot = DB::table('app_notification_user')
        ->where('user_id', $otherUser->id)
        ->where('app_notification_id', $notification->id)
        ->first();

    expect($pivot->dismissed_at)->toBeNull();
});
