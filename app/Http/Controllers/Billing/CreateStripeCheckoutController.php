<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use App\Enums\Teams\TeamMemberPermissionEnum;
use App\Models\Team;
use App\Services\PlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

final readonly class CreateStripeCheckoutController
{
    public function __construct(private PlanService $planService) {}

    public function __invoke(Request $request, string $stripPriceId, Team $team): Response|RedirectResponse
    {
        Gate::authorize(TeamMemberPermissionEnum::BILLING_CHECKOUT_CREATE, $team);

        if ($team->subscribed('default')) {
            return Inertia::location($team->billingPortalUrl(
                route('billing.show', ['team' => $team->id])
            ));
        }

        if (! $this->planService->priceIdExists($stripPriceId)) {
            return back()->with('error', 'Invalid plan ID.');
        }

        $userEmail = $request->user()->email ?? '';

        $team->updateOrCreateStripeCustomer(['email' => $userEmail]);

        $checkout = $team->newSubscription('default', $stripPriceId)
            ->trialDays(14)
            ->checkout([
                'success_url' => route('billing.show', ['success' => 'You have been subscribed', 'team' => $team->id]),
                'cancel_url' => route('billing.show', ['error' => 'The subscription has been canceled', 'team' => $team->id]),
                // 'billing_address_collection' => 'required',
                'customer_update' => [
                    'name' => 'auto',
                    'address' => 'auto',
                ],
                'tax_id_collection' => [
                    'enabled' => true,
                ],
            ]);

        // @phpstan-ignore-next-line
        return Inertia::location($checkout->url);
    }
}
