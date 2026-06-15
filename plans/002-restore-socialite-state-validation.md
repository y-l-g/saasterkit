# Plan 002: Restore Socialite OAuth state validation on web callbacks

> **Executor instructions**: Follow this plan step by step. Run every
> verification command and confirm the expected result before moving on. If a
> STOP condition occurs, stop and report. When done, update this plan's row in
> `plans/README.md`.
>
> **Drift check (run first)**:
> `git diff --stat fcef216..HEAD -- app/Http/Controllers/Auth/Socialite routes/auth.php tests/Feature/Auth`
> On drift, compare the excerpts below against the live code before proceeding.

## Status

- **Priority**: P1
- **Effort**: S
- **Risk**: MED
- **Depends on**: none
- **Category**: security
- **Planned at**: commit `fcef216`, 2026-06-13

## Why this matters

This app uses browser/session OAuth routes. The redirect route uses normal
Socialite session-backed `redirect()`, but the callback calls
`stateless()->user()`, which disables Socialite's session state verification.
For web login and account-linking flows, the callback should call `user()` so
Socialite validates the state generated during redirect.

## Current state

Relevant files:

- `app/Http/Controllers/Auth/Socialite/RedirectToProviderController.php` starts
  provider redirects.
- `app/Http/Controllers/Auth/Socialite/HandleProviderCallbackController.php`
  handles provider callbacks and currently disables state verification.
- `tests/Feature/Auth/SocialiteAuthenticationTest.php` mocks the stateless call,
  so tests currently encode the weaker behavior.

Current callback excerpt:

```php
// app/Http/Controllers/Auth/Socialite/HandleProviderCallbackController.php:26-31
public function __invoke(SocialiteFactory $socialite, string $provider): RedirectResponse
{
    /** @var AbstractProvider $driver */
    $driver = $socialite->driver($provider);
    $socialiteUser = $driver->stateless()->user();
    assert($socialiteUser instanceof SocialiteUser);
```

Current route excerpt:

```php
// routes/auth.php:29-31
Route::get('auth/{provider}/redirect', RedirectToProviderController::class)->name('provider.redirect');

Route::get('auth/{provider}/callback', HandleProviderCallbackController::class)->name('provider.callback');
```

Current test mock excerpt:

```php
// tests/Feature/Auth/SocialiteAuthenticationTest.php:29-32
$provider = Mockery::mock(GoogleProvider::class);
$provider->allows('stateless->user')->andReturn($socialiteUser);

Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
```

Version-specific Socialite docs say `stateless()` disables session state
verification and is useful for stateless APIs. The same docs show web callback
routes retrieving the user with `Socialite::driver('github')->user()`.

## Commands you will need

| Purpose | Command | Expected on success |
| --- | --- | --- |
| Focused auth tests | `php artisan test --compact tests/Feature/Auth/SocialiteAuthenticationTest.php tests/Feature/Auth/LinkProviderOnLoginTest.php` | exit 0 |
| Full PHP tests | `php artisan test --compact` | exit 0 |
| Static analysis | `vendor/bin/phpstan analyse --memory-limit=2G --no-progress` | exit 0 |
| Format check | `vendor/bin/pint --test --format agent` | exit 0 |

## Suggested executor toolkit

- Use `socialite-development` if available.
- Use `pest-testing` if available.
- Use Laravel Boost `search-docs` for `stateless` and Socialite testing if the
  installed APIs differ.

## Scope

**In scope**:

- `app/Http/Controllers/Auth/Socialite/HandleProviderCallbackController.php`
- `tests/Feature/Auth/SocialiteAuthenticationTest.php`
- `tests/Feature/Auth/LinkProviderOnLoginTest.php`

**Out of scope**:

- Signed login links. That is plan 001.
- Adding new OAuth providers.
- Reworking account-linking product behavior.
- Adding provider enum route constraints. That can be a follow-up hardening
  change after this state-validation fix.

## Git workflow

- Branch: `advisor/002-restore-socialite-state-validation`
- Commit message: `Restore Socialite state validation`
- Do not push unless instructed.

## Steps

### Step 1: Update tests to expect stateful callback retrieval

Change the Socialite provider mock helper so it stubs `user()` instead of
`stateless->user`.

The helper should continue returning a `Laravel\Socialite\Two\User` mock with
`getId`, `getName`, `getEmail`, `token`, and `refreshToken` populated.

Add an assertion or test guard that prevents the old stateless method from being
called. With Mockery, it is acceptable to only stub `user()` and let the test
fail if the production code still calls `stateless()`.

**Verify**:
`php artisan test --compact tests/Feature/Auth/SocialiteAuthenticationTest.php`
Expected before Step 2: tests fail because production still calls
`stateless()->user()`.

### Step 2: Use Socialite's stateful `user()` callback method

In `HandleProviderCallbackController`, replace:

```php
$socialiteUser = $driver->stateless()->user();
```

with:

```php
$socialiteUser = $driver->user();
```

Keep the existing `assert($socialiteUser instanceof SocialiteUser);` unless
static analysis requires a better PHPDoc assertion.

Do not add `stateless()` anywhere else in this web callback path.

**Verify**:
`php artisan test --compact tests/Feature/Auth/SocialiteAuthenticationTest.php tests/Feature/Auth/LinkProviderOnLoginTest.php`
Expected: all selected tests pass.

### Step 3: Search for stale stateless callback usage

Run:

```bash
rg -n "stateless\\(\\)->user|stateless->user" app tests routes
```

Expected: no matches in first-party app/tests/routes files. If a match remains
in a non-web API path that was added after this plan, stop and report rather
than changing it.

### Step 4: Run the standard checks

Run:

```bash
php artisan test --compact
vendor/bin/phpstan analyse --memory-limit=2G --no-progress
vendor/bin/pint --test --format agent
```

Expected: all exit 0.

## Test plan

Keep tests in Pest style. Existing Socialite tests use a local
`mockSocialiteProvider()` helper and `RefreshDatabase`; continue that style.
The important regression is that tests mock `user()` and fail if production uses
`stateless()`.

## Done criteria

- [ ] `HandleProviderCallbackController` calls `$driver->user()`.
- [ ] First-party source and tests have no `stateless()->user` or
  `stateless->user` match.
- [ ] Existing Socialite registration, account linking, existing-user login,
  and same-email login-link tests pass.
- [ ] Full PHP tests, Larastan, and Pint check pass.

## STOP conditions

- The app has gained a separate stateless API OAuth callback. Do not change it
  under this plan.
- Removing `stateless()` breaks tests because the redirect route is also
  stateless. Stop and inspect the full Socialite flow before proceeding.
- A fix requires changing provider configuration or adding new providers.

## Maintenance notes

If a future mobile/API OAuth flow needs stateless Socialite behavior, implement
it on a separate route and controller so the web login/linking path keeps state
verification.

