# Plan 003: Move billing session creation routes to POST requests

> **Executor instructions**: Follow this plan exactly. Run each verification
> command. Stop and report if a STOP condition occurs. When done, update this
> plan's row in `plans/README.md`.
>
> **Drift check (run first)**:
> `git diff --stat fcef216..HEAD -- routes/billing.php app/Http/Controllers/Billing resources/js/pages/settings/Billing.vue tests/Feature/Billing`
> If any in-scope file changed, compare the excerpts below with the live code.

## Status

- **Priority**: P1
- **Effort**: M
- **Risk**: MED
- **Depends on**: none
- **Category**: security, bug
- **Planned at**: commit `fcef216`, 2026-06-13

## Why this matters

The billing checkout route is a `GET` route but creates or updates a Stripe
customer and creates a Stripe Checkout session. The billing portal route also
creates an external portal session. GET requests are easier to trigger
accidentally via link previews, prefetching, crawlers, or repeated browser
loads. Moving these session-creating actions to POST makes them explicit
same-origin actions and brings them under Laravel's CSRF protections.

## Current state

Relevant files:

- `routes/billing.php` declares billing settings, checkout, and portal routes.
- `CreateStripeCheckoutController` creates Stripe customer/checkout state.
- `RedirectToBillingPortalController` creates a Stripe billing portal URL.
- `resources/js/pages/settings/Billing.vue` calls both with `router.get`.
- `tests/Feature/Billing/BillingTest.php` asserts GET behavior.

Current routes:

```php
// routes/billing.php:10-15
Route::get('settings/billing', ShowBillingSettingsController::class)->name('billing.show');

Route::get('settings/billing/checkout/{stripePriceId}', CreateStripeCheckoutController::class)
    ->name('billing.checkout');

Route::get('settings/billing/portal', RedirectToBillingPortalController::class)
    ->name('billing.portal');
```

Checkout creates external state:

```php
// app/Http/Controllers/Billing/CreateStripeCheckoutController.php:36-42
$userEmail = $request->user()->email ?? '';

$team->updateOrCreateStripeCustomer(['email' => $userEmail]);

$checkout = $team->newSubscription('default', $stripePriceId)
    ->trialDays(14)
    ->checkout([
```

Frontend currently uses GET:

```ts
// resources/js/pages/settings/Billing.vue:72-79
router.get(
    checkout({
        current_team: props.team.slug,
        stripePriceId: stripePriceId,
    }).url,
    {},
    { onFinish: () => (isProcessingCheckout.value = false) },
);
```

Laravel 13 CSRF docs say POST, PUT, PATCH, and DELETE requests in web routes are
checked for CSRF tokens. Cashier docs show simple GET examples for Checkout, but
this app creates Stripe customer/session state from authenticated UI controls,
so this plan intentionally uses POST for stronger application semantics.

## Commands you will need

| Purpose | Command | Expected on success |
| --- | --- | --- |
| Focused tests | `php artisan test --compact tests/Feature/Billing/BillingTest.php tests/Feature/Billing/BillingSettingsTest.php` | exit 0 |
| Route check | `php artisan route:list --path=settings/billing --no-interaction` | checkout and portal show POST, settings show GET |
| Frontend typecheck | `npm run types:check` | exit 0 |
| Full PHP tests | `php artisan test --compact` | exit 0 |
| Lint/format | `npm run lint:check && npm run format:check && vendor/bin/pint --test --format agent` | exit 0 |

## Suggested executor toolkit

- Use `cashier-stripe-development` for Cashier route/controller behavior.
- Use `wayfinder-development` because route methods and generated frontend route
  helpers may need regeneration.
- Use `pest-testing` for feature tests.

## Scope

**In scope**:

- `routes/billing.php`
- `app/Http/Controllers/Billing/CreateStripeCheckoutController.php`
- `app/Http/Controllers/Billing/RedirectToBillingPortalController.php`
- `resources/js/pages/settings/Billing.vue`
- `tests/Feature/Billing/BillingTest.php`
- Generated Wayfinder route files only if this repo requires them to be
  regenerated and tracked. Current `.gitignore` ignores `resources/js/routes`,
  `resources/js/actions`, and `resources/js/wayfinder`, so do not commit them
  unless the repo state has changed.

**Out of scope**:

- Stripe webhook setup.
- Subscription plan/pricing changes.
- Billing settings display or invoice loading.
- Replacing Cashier's checkout implementation.

## Git workflow

- Branch: `advisor/003-post-billing-session-actions`
- Commit message: `Use POST for billing session actions`
- Do not push unless instructed.

## Steps

### Step 1: Change session-creating routes to POST

In `routes/billing.php`, keep billing settings as GET. Change checkout and
portal routes to POST:

```php
Route::post('settings/billing/checkout/{stripePriceId}', CreateStripeCheckoutController::class)
    ->name('billing.checkout');

Route::post('settings/billing/portal', RedirectToBillingPortalController::class)
    ->name('billing.portal');
```

Do not change route names. Existing Wayfinder imports should continue to resolve
after regeneration.

**Verify**:
`php artisan route:list --path=settings/billing --no-interaction`
Expected: `settings/billing` is GET; checkout and portal are POST.

### Step 2: Update frontend calls to POST

In `resources/js/pages/settings/Billing.vue`:

- Change `router.get(checkout(...).url, {}, options)` to `router.post(...)`.
- Change `router.get(portal(...).url, {}, options)` to `router.post(...)`.
- Preserve the existing `onFinish` behavior for loading states.

If Wayfinder exposes method-specific helpers for these routes, prefer using the
POST route object rather than hardcoding URLs.

**Verify**:
`npm run types:check`
Expected: exit 0.

### Step 3: Update billing feature tests

In `tests/Feature/Billing/BillingTest.php`:

- Replace the checkout GET request with POST.
- Replace the portal GET request with POST.
- Add two method-regression assertions:
  - GET checkout no longer redirects to Stripe. Accept either 405 or redirect
    back, depending on Laravel route behavior; prefer `assertMethodNotAllowed()`
    if available.
  - GET portal no longer redirects to Stripe.

Keep the existing Stripe fake/mocking pattern from `tests/TestCase.php`.

**Verify**:
`php artisan test --compact tests/Feature/Billing/BillingTest.php tests/Feature/Billing/BillingSettingsTest.php`
Expected: all pass.

### Step 4: Regenerate Wayfinder routes if needed

Run:

```bash
php artisan wayfinder:generate --with-form --no-interaction
```

Then inspect `git status --short resources/js/routes resources/js/actions resources/js/wayfinder`.

Expected in this repo: generated route directories are ignored, so there should
be no tracked diff for them. If tracked generated files appear because repo
state changed, include only the minimal generated files needed for typecheck.

**Verify**:
`npm run types:check`
Expected: exit 0.

### Step 5: Run broad checks

Run:

```bash
php artisan test --compact
npm run types:check
npm run lint:check
npm run format:check
vendor/bin/phpstan analyse --memory-limit=2G --no-progress
vendor/bin/pint --test --format agent
```

Expected: all exit 0.

## Test plan

Update existing Pest billing feature tests. Add method-regression assertions so
future route changes cannot silently reintroduce GET-triggered Stripe sessions.

## Done criteria

- [ ] Checkout and portal session routes are POST.
- [ ] Frontend uses POST for checkout and portal actions.
- [ ] GET requests to checkout/portal no longer reach Stripe redirect behavior.
- [ ] Focused billing tests, full PHP tests, frontend typecheck, lint, format,
  Larastan, and Pint check all pass.

## STOP conditions

- Wayfinder generation produces large unrelated changes outside billing routes.
- Cashier/Inertia cannot return `Inertia::location()` from a POST response in
  this app. Stop and report with the failing response/test output.
- Product owner explicitly requires shareable billing portal links. That is a
  different product requirement and should be handled outside this plan.

## Maintenance notes

Future billing actions that create Stripe sessions should default to POST unless
there is a documented reason to make them linkable GET routes.

