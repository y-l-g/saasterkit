<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final readonly class AdminDashboardShowController
{
    public function __invoke(Request $request): Response
    {
        $period = 30;

        $thisPeriodStart = now()->subDays($period);
        $previousPeriodStart = now()->subDays($period * 2);

        $newSubscriptionsThisPeriod = Subscription::query()
            ->where('created_at', '>=', $thisPeriodStart)
            ->count();
        $newSubscriptionsPreviousPeriod = Subscription::query()
            ->whereBetween('created_at', [$previousPeriodStart, $thisPeriodStart])
            ->count();

        $canceledSubscriptionsThisPeriod = Subscription::query()
            ->whereNotNull('canceled_at')
            ->where('canceled_at', '>=', $thisPeriodStart)
            ->count();
        $canceledSubscriptionsPreviousPeriod = Subscription::query()
            ->whereNotNull('canceled_at')
            ->whereBetween('canceled_at', [$previousPeriodStart, $thisPeriodStart])
            ->count();

        $newUsersThisPeriod = User::query()
            ->where('created_at', '>=', $thisPeriodStart)
            ->count();
        $newUsersPreviousPeriod = User::query()
            ->whereBetween('created_at', [$previousPeriodStart, $thisPeriodStart])
            ->count();

        return Inertia::render('admin/AdminDashboard', [
            'monthPerformance' => [
                'newSubscriptions' => [
                    'value' => $newSubscriptionsThisPeriod,
                    'variation' => $this->calculateVariation($newSubscriptionsThisPeriod, $newSubscriptionsPreviousPeriod),
                ],
                'canceledSubscriptions' => [
                    'value' => $canceledSubscriptionsThisPeriod,
                    'variation' => $this->calculateVariation($canceledSubscriptionsThisPeriod, $canceledSubscriptionsPreviousPeriod),
                ],
                'newUsers' => [
                    'value' => $newUsersThisPeriod,
                    'variation' => $this->calculateVariation($newUsersThisPeriod, $newUsersPreviousPeriod),
                ],
            ],
            'currentOverview' => [
                'activeSubscriptions' => Subscription::query()->active()->count(),
                'subscriptionsOnTrial' => Subscription::query()->onTrial()->count(),
                'subscriptionsOnGracePeriod' => Subscription::query()->onGracePeriod()->count(),
            ],
        ]);
    }

    protected function calculateVariation(int $current, int $previous): int
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (int) round((($current - $previous) / $previous) * 100);
    }
}
