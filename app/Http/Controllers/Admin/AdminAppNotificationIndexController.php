<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Inertia\Response;

final readonly class AdminAppNotificationIndexController
{
    public function __invoke(): Response
    {
        return Inertia::render('admin/AdminNotifications');
    }
}
