<?php

declare(strict_types=1);

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
});

it('denies non admin users from viewing the notifications page', function (): void {
    actingAs($this->user)->get(route('admin.notifications.index'))->assertForbidden();
});

it('allows admin users to view the notifications page', function (): void {
    actingAs($this->admin)->get(route('admin.notifications.index'))->assertOk();
});

it('denies non admin users from sending a notification', function (): void {
    actingAs($this->user)
        ->post(route('admin.notifications.store'), ['title' => 'Test', 'body' => 'Test Body'])
        ->assertForbidden();
});

it('allows an admin user to send a notification to all users', function (): void {
    User::factory()->count(5)->create();

    actingAs($this->admin)
        ->post(route('admin.notifications.store'), ['title' => 'Test Title', 'body' => 'Test Body'])
        ->assertRedirect()
        ->assertSessionHas('success', 'Notification sent to all users.');

    assertDatabaseCount('app_notifications', 1);
    $notification = \App\Models\AppNotification::query()->first();
    assertDatabaseCount('app_notification_user', \App\Models\User::query()->count());
    expect($notification->title)->toBe('Test Title');
});

it('fails validation when data is missing', function (): void {
    actingAs($this->admin)
        ->post(route('admin.notifications.store'), [])
        ->assertSessionHasErrors(['title', 'body']);
});
