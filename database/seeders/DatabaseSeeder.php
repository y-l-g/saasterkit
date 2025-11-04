<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        User::factory()->count(20)->create();

        collect(['active', 'trialing', 'canceled'])
            ->each(function ($status) {
                Team::factory()
                    ->count(15)
                    ->withSubscription($status)
                    ->has(User::factory()->count(rand(2, 5)), 'users')
                    ->has(TeamInvitation::factory()->count(rand(1, 3)))
                    ->create();
            });

        User::factory()
            ->count(5)
            ->has(Team::factory()->count(rand(1, 3)))
            ->create();

        $allUsers = User::all();

        AppNotification::factory()
            ->count(5)
            ->create()
            ->each(function (AppNotification $notification) use ($allUsers) {
                $usersToNotify = $allUsers->random(10);
                $pivotData = [];
                foreach ($usersToNotify as $user) {
                    $isDismissed = rand(1, 10) <= 3;
                    $pivotData[$user->id] = [
                        'dismissed_at' => $isDismissed ? fake()->dateTimeBetween($notification->created_at, 'now') : null,
                    ];
                }
                $notification->users()->attach($pivotData);
            });
    }
}
