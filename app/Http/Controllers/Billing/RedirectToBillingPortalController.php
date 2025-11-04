<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

final readonly class RedirectToBillingPortalController
{
    public function __invoke(Request $request, Team $team): Response|RedirectResponse
    {
        Gate::authorize(TeamMemberPermissionEnum::BILLING_PORTAL_VIEW, $team);

        return Inertia::location($team->billingPortalUrl(route('billing.show', ['team' => $team->id])));
    }
}
