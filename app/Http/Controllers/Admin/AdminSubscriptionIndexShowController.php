<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Data\Billing\SubscriptionData;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final readonly class AdminSubscriptionIndexShowController
{
    public function __invoke(Request $request): Response
    {
        // @phpstan-ignore-next-line
        $subscriptions = QueryBuilder::for(Subscription::class, $request)
            ->with(['team.owner'])
            ->allowedFilters([
                AllowedFilter::scope('search'),
                AllowedFilter::scope('period'),
                AllowedFilter::scope('status'),
                AllowedFilter::scope('plan'),
            ])
            ->allowedSorts([
                'id',
                'ends_at',
                'created_at',
            ])
            ->defaultSort('-created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/AdminSubscriptionIndex', [
            'subscriptions' => SubscriptionData::collect($subscriptions),
        ]);
    }
}
