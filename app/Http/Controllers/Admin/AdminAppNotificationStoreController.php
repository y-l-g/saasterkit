<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminAppNotificationStoreRequest;
use App\Services\AppNotificationService;
use Illuminate\Http\RedirectResponse;

final readonly class AdminAppNotificationStoreController
{
    public function __construct(
        private AppNotificationService $notificationService
    ) {}

    public function __invoke(AdminAppNotificationStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->notificationService->sendToAll(
            title: $data['title'],
            body: $data['body']
        );

        return back()->with('success', 'Notification sent to all users.');
    }
}
