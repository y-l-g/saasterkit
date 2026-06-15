# Plan 006: Add missing relationship constraints and prevent orphaned team billing rows

> **Executor instructions**: Read fully, then execute step by step. Run every
> verification command. Stop and report on STOP conditions. Update this plan's
> row in `plans/README.md` when complete.
>
> **Drift check (run first)**:
> `git diff --stat fcef216..HEAD -- database/migrations app/Models/Team.php tests/Feature/Teams tests/Feature/Billing`
> If in-scope files changed, compare excerpts below against the live code.

## Status

- **Priority**: P2
- **Effort**: M
- **Risk**: HIGH
- **Depends on**: none
- **Category**: migration, bug
- **Planned at**: commit `fcef216`, 2026-06-13

## Why this matters

Several relationship columns are plain `foreignId()` columns without foreign key
constraints. Neighboring tables already use `constrained()->cascadeOnDelete()`,
so this is inconsistent and allows orphaned rows. In particular, `Team::purge()`
detaches members and deletes the team, but ended subscription rows can remain
because nothing constrains or deletes them.

## Current state

Relevant files:

- `database/migrations/2025_09_26_082959_create_subscriptions_table.php`
- `database/migrations/2025_09_26_083000_create_subscription_items_table.php`
- `database/migrations/2025_10_17_000002_create_team_user_table.php`
- `app/Models/Team.php`
- `tests/Feature/Teams/TeamDeletionTest.php`

Current unconstrained columns:

```php
// database/migrations/2025_09_26_082959_create_subscriptions_table.php:14-18
Schema::create('subscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('team_id');
```

```php
// database/migrations/2025_09_26_083000_create_subscription_items_table.php:16-19
Schema::create('subscription_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('subscription_id');
```

```php
// database/migrations/2025_10_17_000002_create_team_user_table.php:13-20
Schema::create('team_user', function (Blueprint $table) {
    $table->id();
    $table->foreignId('team_id');
    $table->foreignId('user_id');
    $table->string('role')->nullable();
    $table->timestamps();

    $table->unique(['team_id', 'user_id']);
});
```

Current team purge:

```php
// app/Models/Team.php:160-166
public function purge(): void
{
    DB::transaction(function (): void {
        User::query()->where('current_team_id', $this->id)
            ->update(['current_team_id' => null]);
        $this->users()->detach();
        $this->delete();
    });
}
```

Neighboring migration pattern:

```php
// database/migrations/2025_10_17_000003_create_team_invitations_table.php:15
$table->foreignId('team_id')->constrained()->cascadeOnDelete();
```

## Commands you will need

| Purpose | Command | Expected on success |
| --- | --- | --- |
| Make migration | `php artisan make:migration add_missing_foreign_keys_to_team_billing_tables --no-interaction` | creates one migration |
| Focused tests | `php artisan test --compact tests/Feature/Teams/TeamDeletionTest.php tests/Feature/Billing/BillingSettingsTest.php` | exit 0 |
| Full tests | `php artisan test --compact` | exit 0 |
| Static/format | `vendor/bin/phpstan analyse --memory-limit=2G --no-progress && vendor/bin/pint --test --format agent` | exit 0 |

## Suggested executor toolkit

- Use `laravel-best-practices` for migrations and model deletion behavior.
- Use `cashier-stripe-development` when reasoning about Cashier subscription
  tables.
- Use `pest-testing` for feature tests.

## Scope

**In scope**:

- One new migration under `database/migrations`
- `app/Models/Team.php`
- `tests/Feature/Teams/TeamDeletionTest.php`
- `tests/Feature/Billing/BillingSettingsTest.php` only if billing setup needs
  a regression test.

**Out of scope**:

- Rewriting old migrations that may have run in production.
- Changing subscription cancellation policy.
- Changing Stripe webhook behavior.
- Adding constraints to `users.current_team_id` unless a reviewer explicitly
  expands this plan.

## Git workflow

- Branch: `advisor/006-add-relationship-constraints`
- Commit message: `Add missing relationship constraints`
- Do not push unless instructed.

## Steps

### Step 1: Confirm production migration policy

Do not edit the existing dated migrations unless the operator confirms this
starter kit has no production migration history. The default path is an additive
new migration.

Create a new migration:

```bash
php artisan make:migration add_missing_foreign_keys_to_team_billing_tables --no-interaction
```

**Verify**:
`ls database/migrations/*add_missing_foreign_keys_to_team_billing_tables.php`
Expected: exactly one new migration file.

### Step 2: Clean orphan-prone deletion behavior in `Team::purge()`

Before adding database constraints, make `Team::purge()` explicitly remove
subscription data when deletion is allowed.

Target behavior:

- Inside the existing transaction, delete subscription items for the team's
  subscriptions, then delete the team's subscriptions.
- Then detach users and delete the team.
- Keep the existing `TeamDestroyRequest` rule that blocks deletion when
  `$team->subscribed('default')` is true.

Use Eloquent relationships where practical. If a bulk query is clearer, keep it
inside the transaction and avoid raw SQL.

**Verify**:
Add/extend `tests/Feature/Teams/TeamDeletionTest.php` so deleting a team with an
ended subscription also removes rows from `subscriptions` and
`subscription_items`. Run:

```bash
php artisan test --compact tests/Feature/Teams/TeamDeletionTest.php
```

Expected: all tests pass.

### Step 3: Add foreign key constraints for PostgreSQL/MySQL deployments

In the new migration:

- Add a foreign key from `subscriptions.team_id` to `teams.id`. Use
  `restrictOnDelete()` so direct deletion of a team with remaining subscriptions
  fails instead of silently deleting billing history.
- Add a foreign key from `subscription_items.subscription_id` to
  `subscriptions.id` with `cascadeOnDelete()`.
- Add foreign keys from `team_user.team_id` to `teams.id` and
  `team_user.user_id` to `users.id`, both with `cascadeOnDelete()`.

SQLite cannot reliably add foreign keys to already-created tables via
`ALTER TABLE`. Since this repo's tests use SQLite in-memory migrations, guard the
constraint-alter portion:

```php
if (DB::connection()->getDriverName() === 'sqlite') {
    return;
}
```

Before adding constraints on PostgreSQL/MySQL, check for orphan rows. If any
exist, throw a `RuntimeException` with counts but no row dumps. This prevents a
partial deploy from failing with an opaque database error.

The `down()` method should drop the named constraints for non-SQLite drivers.
Use explicit constraint names so rollback is deterministic.

**Verify**:
`php artisan test --compact tests/Feature/Teams/TeamDeletionTest.php`
Expected: SQLite tests still pass.

### Step 4: Add a migration-focused safety test if feasible

Because SQLite skips the actual constraint alters, the main automated coverage
is the `Team::purge()` behavior. If this repo already has migration tests or a
PostgreSQL test connection available, add a small test that confirms constraints
exist. If not, do not invent a custom test harness.

**Verify**:
`php artisan test --compact tests/Feature/Teams/TeamDeletionTest.php tests/Feature/Billing/BillingSettingsTest.php`
Expected: all pass.

### Step 5: Run broad checks

Run:

```bash
php artisan test --compact
vendor/bin/phpstan analyse --memory-limit=2G --no-progress
vendor/bin/pint --test --format agent
```

Expected: all exit 0.

## Test plan

Extend `TeamDeletionTest` with an ended-subscription deletion case that creates:

- a team,
- a canceled subscription with `ends_at` in the past,
- at least one subscription item for that subscription.

Assert team, subscription, and subscription item rows are gone after deletion.
Existing active/grace-period tests should remain unchanged and continue to
block deletion.

## Done criteria

- [ ] `Team::purge()` removes allowed subscription and subscription item rows.
- [ ] A new migration adds or prepares named constraints for non-SQLite drivers.
- [ ] Migration fails early with orphan counts on PostgreSQL/MySQL if data is
  dirty.
- [ ] SQLite test suite remains green.
- [ ] Full PHP tests, Larastan, and Pint check pass.

## STOP conditions

- The operator says the old migrations are safe to rewrite instead of using an
  additive migration. Stop and ask for explicit scope confirmation.
- Existing production data contains orphans and the desired cleanup policy is
  unclear.
- Adding constraints requires broad Cashier table rewrites or webhook changes.
- SQLite cannot run the migration even with the driver guard.

## Maintenance notes

Reviewers should scrutinize deletion semantics. Billing history may have legal
or accounting value; this plan deletes local ended subscription rows only when
the team itself is deleted through the app's existing deletion flow.

