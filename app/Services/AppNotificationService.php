<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class AppNotificationService
{
    public function sendToUser(User $user, string $title, string $body): AppNotification
    {
        return DB::transaction(function () use ($user, $title, $body) {
            $notification = AppNotification::query()->create([
                'title' => $title,
                'body' => $body,
            ]);

            $notification->users()->attach($user->id);

            return $notification;
        });
    }

    public function sendToTeam(Team $team, string $title, string $body): AppNotification
    {
        return DB::transaction(function () use ($team, $title, $body) {
            $notification = AppNotification::query()->create([
                'title' => $title,
                'body' => $body,
            ]);

            $userIds = $team->users()->pluck('id');
            $notification->users()->attach($userIds);

            return $notification;
        });
    }

    public function sendToAll(string $title, string $body): AppNotification
    {
        return DB::transaction(function () use ($title, $body) {
            $notification = AppNotification::query()->create([
                'title' => $title,
                'body' => $body,
            ]);

            $userIds = User::query()->pluck('id');
            $notification->users()->attach($userIds);

            return $notification;
        });
    }
}
