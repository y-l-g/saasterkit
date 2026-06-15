# Plan 001: Harden signed login links for two-factor users and team redirects

> **Executor instructions**: Follow this plan step by step. Run every
> verification command and confirm the expected result before moving to the
> next step. If anything in the "STOP conditions" section occurs, stop and
> report. When done, update this plan's row in `plans/README.md`.
>
> **Drift check (run first)**:
> `git diff --stat fcef216..HEAD -- app/Http/Controllers/Auth/LoginWithLinkController.php app/Notifications/LoginLinkNotification.php routes/auth.php tests/Feature/Auth`
> If any in-scope file changed since this plan was written, compare the
> "Current state" excerpts against the live code before proceeding. On a
> mismatch, stop and report.

## Status

- **Priority**: P1
- **Effort**: S
- **Risk**: MED
- **Depends on**: none
- **Category**: security, bug
- **Planned at**: commit `fcef216`, 2026-06-13

## Why this matters

The signed login link currently logs a user in directly. That bypasses the
Fortify two-factor challenge for users who enabled 2FA, even though normal
password and Socialite login paths challenge those users. The same controller
also redirects to `profile.edit` without a required `{current_team}` parameter,
which raises `UrlGenerationException` after successful login for users with a
team. This plan makes signed login links follow the same 2FA semantics as the
other login paths and redirects safely after authentication.

## Current state

- `app/Http/Controllers/Auth/LoginWithLinkController.php` handles the signed
  magic-link route.
- `app/Notifications/LoginLinkNotification.php` creates a 5-minute signed URL.
- `routes/auth.php` exposes the signed login link route under `guest` and
  `signed` middleware.
- `tests/Feature/Auth/AuthenticationTest.php` already covers password login
  2FA behavior. There is no signed-login-link test.

Current controller excerpt:

```php
// app/Http/Controllers/Auth/LoginWithLinkController.php:18-24
public function __invoke(Request $request, User $user): RedirectResponse
{
    $this->guard->login($user);

    $request->session()->regenerate();

    return to_route('profile.edit')->with('success', 'You have been logged in successfully.');
}
```

Current notification excerpt:

```php
// app/Notifications/LoginLinkNotification.php:40-44
$loginUrl = URL::temporarySignedRoute(
    'auth.login.link',
    now()->addMinutes(5),
    ['user' => $notifiable->id]
);
```

Relevant normal-login test pattern:

```php
// tests/Feature/Auth/AuthenticationTest.php:31-45
it('redirects users with two-factor enabled to the two-factor challenge', function (): void {
    $user = User::factory()->withTwoFactor()->create();

    $response = post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $response->assertSessionHas('login.id', $user->id);
    assertGuest();
});
```

Fortify docs for this app version say that when 2FA is enabled, authentication
redirects to the two-factor challenge screen, and the challenge POST accepts
either a TOTP `code` or `recovery_code`.

## Commands you will need

| Purpose | Command | Expected on success |
| --- | --- | --- |
| Focused tests | `php artisan test --compact tests/Feature/Auth/AuthenticationTest.php tests/Feature/Auth/LinkProviderOnLoginTest.php` | exit 0, all tests pass |
| Full PHP tests | `php artisan test --compact` | exit 0, 141+ tests pass |
| Static analysis | `vendor/bin/phpstan analyse --memory-limit=2G --no-progress` | exit 0, no errors |
| Format check | `vendor/bin/pint --test --format agent` | exit 0 |

## Suggested executor toolkit

- Use the `fortify-development` skill if available. This plan modifies
  authentication and 2FA behavior.
- Use the `pest-testing` skill if available. This plan adds Pest feature tests.
- Use Laravel Boost `search-docs` for Fortify two-factor authentication if
  current APIs differ from the excerpts.

## Scope

**In scope**:

- `app/Http/Controllers/Auth/LoginWithLinkController.php`
- `tests/Feature/Auth/AuthenticationTest.php` or a new
  `tests/Feature/Auth/LoginLinkAuthenticationTest.php`
- `app/Notifications/LoginLinkNotification.php` only if type annotations need
  cleanup while touching this flow.

**Out of scope**:

- Socialite callback state validation. That is plan 002.
- Changing signed URL lifetime.
- Changing the email copy, except if needed for a test fixture.
- Adding passwordless login features beyond the existing signed-link flow.

## Git workflow

- Branch: `advisor/001-harden-signed-login-links`
- Commit message style: use concise imperative style from recent history, e.g.
  `Harden signed login links`.
- Do not push or open a PR unless the operator asked.

## Steps

### Step 1: Add signed-login tests

Add tests covering the current missing behavior. Prefer a new file
`tests/Feature/Auth/LoginLinkAuthenticationTest.php` if that keeps
`AuthenticationTest.php` focused.

Required cases:

- A signed login link logs in a user without 2FA and redirects to the profile
  page for the current team.
- A signed login link for a user with confirmed 2FA redirects to
  `route('two-factor.login')`, stores `login.id` in the session, and leaves the
  user unauthenticated.
- A signed login link for a user with no team redirects to onboarding after
  successful login.
- An unsigned or expired signed URL remains forbidden or invalid according to
  Laravel's signed middleware behavior.

Use `URL::temporarySignedRoute('auth.login.link', now()->addMinutes(5), ['user' => $user->id])`
in the tests. For 2FA users, use `User::factory()->withTwoFactor()->create()`
to match existing tests.

**Verify**:
`php artisan test --compact tests/Feature/Auth/LoginLinkAuthenticationTest.php`
or the exact file you added. Expected: the new 2FA/redirect tests fail before
the controller change and pass after Step 2.

### Step 2: Challenge 2FA users instead of logging them in

Update `LoginWithLinkController`.

Target behavior:

- If `$user->hasEnabledTwoFactorAuthentication()` is true, store the same
  session keys used by the existing Socialite controller:
  `login.id` and `login.remember`.
- Dispatch `Laravel\Fortify\Events\TwoFactorAuthenticationChallenged`.
- Redirect to `two-factor.login`.
- Do not call `$this->guard->login($user)` in that branch.

Use `login.remember => false` unless a reviewer explicitly asks for magic-link
logins to remember the browser. This is a login link, not a remember-me flow.

**Verify**:
`php artisan test --compact tests/Feature/Auth/LoginLinkAuthenticationTest.php`
Expected: all signed login-link tests pass.

### Step 3: Fix the successful redirect target

After a non-2FA signed-link login:

- Regenerate the session.
- If `$user->currentTeam` exists, redirect to
  `to_route('profile.edit', ['current_team' => $user->currentTeam->slug])`.
- If no current team exists, redirect to `to_route('onboarding')`.
- Preserve the existing success flash message.

Do not use bare `to_route('profile.edit')`; it cannot generate a URL because
`profile.edit` is under the `{current_team:slug}` route prefix.

**Verify**:

- `php artisan tinker --execute 'try { echo route("profile.edit"); } catch (Throwable $e) { echo get_class($e); }'`
  should still print `Illuminate\Routing\Exceptions\UrlGenerationException`.
  This confirms the controller must pass the team parameter.
- `php artisan test --compact tests/Feature/Auth/LoginLinkAuthenticationTest.php`
  should pass.

### Step 4: Run focused and broad checks

Run:

```bash
php artisan test --compact tests/Feature/Auth/AuthenticationTest.php tests/Feature/Auth/LinkProviderOnLoginTest.php tests/Feature/Auth/LoginLinkAuthenticationTest.php
vendor/bin/phpstan analyse --memory-limit=2G --no-progress
vendor/bin/pint --test --format agent
```

Expected: all commands exit 0.

## Test plan

Use Pest feature tests with `uses(RefreshDatabase::class)`, matching existing
auth tests. The new tests should assert authentication state with
`assertAuthenticated()`, `assertGuest()`, redirects with `assertRedirect()`, and
session keys with `assertSessionHas()`.

## Done criteria

- [ ] Signed login-link tests cover non-2FA login, 2FA challenge, team redirect,
  no-team redirect, and invalid signature behavior.
- [ ] 2FA users are not authenticated by the signed link before completing the
  Fortify challenge.
- [ ] `LoginWithLinkController` never calls bare `to_route('profile.edit')`.
- [ ] Focused tests, full PHP tests, Larastan, and Pint check all pass.
- [ ] No files outside the in-scope list are modified except `plans/README.md`
  status if you were asked to update it.

## STOP conditions

- The current controller no longer matches the excerpt.
- Fortify's `hasEnabledTwoFactorAuthentication()` method is unavailable on
  `User`; stop and verify the installed Fortify API before improvising.
- The fix appears to require changing Socialite callback behavior. That belongs
  in plan 002.
- The focused test fails twice after a reasonable fix attempt.

## Maintenance notes

Reviewers should check that signed links do not become a second authentication
system with weaker semantics than Fortify. Future passwordless-login work should
share the same 2FA challenge branch instead of adding another direct login path.

