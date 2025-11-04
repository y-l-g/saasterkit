<?php

declare(strict_types=1);

namespace App\Http\Controllers\AppNotifications;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class AppNotificationDismissController
{
    public function __invoke(Request $request, AppNotification $notification): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->notifications()->updateExistingPivot($notification->id, [
            'dismissed_at' => now(),
        ]);

        return back()->with('success', 'Notification dismissed.');
    }
}
