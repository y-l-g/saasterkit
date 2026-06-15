# Plan 004: Align the team admin role with its advertised permissions

> **Executor instructions**: Follow the steps and run every verification
> command. Stop if a STOP condition occurs. When done, update this plan's row in
> `plans/README.md`.
>
> **Drift check (run first)**:
> `git diff --stat fcef216..HEAD -- app/Enums/Teams app/Models/Concerns/HasTeams.php app/Services/RoleService.php resources/js/pages/teams tests/Feature/Teams tests/Feature/Billing`
> Compare the excerpts below if any in-scope file changed.

## Status

- **Priority**: P1
- **Effort**: S
- **Risk**: MED
- **Depends on**: none
- **Category**: bug
- **Planned at**: commit `fcef216`, 2026-06-13

## Why this matters

The UI and data layer describe `admin` team members as administrators who can
perform any action. The server-side permission array only grants view and
update, so admin members cannot invite users, manage members, transfer
ownership, delete teams, or manage billing. This creates a confusing role model
and hides team-management UI controls from users who were assigned the admin
role.

## Current state

Relevant files:

- `app/Enums/Teams/RoleEnum.php` is the source of role names,
  descriptions, and permissions.
- `app/Models/Concerns/HasTeams.php` checks role permissions for non-owners.
- `resources/js/pages/teams/TeamSettings.vue` hides controls based on the
  shared permissions payload.
- Feature tests cover owner behavior but do not assert admin member capability.

Current role excerpt:

```php
// app/Enums/Teams/RoleEnum.php:23-44
public function description(): string
{
    return match ($this) {
        self::ADMIN => 'Administrator users can perform any action.',
        self::EDITOR => 'Editor users have the ability to read, create, and update.',
    };
}

public function permissions(): array
{
    return match ($this) {
        self::ADMIN => [
            TeamMemberPermissionEnum::TEAM_VIEW,
            TeamMemberPermissionEnum::TEAM_UPDATE,
        ],
        self::EDITOR => [
            TeamMemberPermissionEnum::TEAM_VIEW,
        ],
    };
}
```

Current permission check excerpt:

```php
// app/Models/Concerns/HasTeams.php:94-104
public function hasTeamPermission(Team $team, TeamMemberPermissionEnum $permission): bool
{
    if ($this->ownsTeam($team)) {
        return true;
    }
    $role = $this->teamRole($team);
    if (! $role) {
        return false;
    }

    return in_array($permission, $role->permissions(), true);
}
```

Current UI controls depend on the permission payload:

```vue
<!-- resources/js/pages/teams/TeamSettings.vue:37-51 -->
<TeamSendInvitation v-if="hasTeamPermission('team.member.invite')" />
<TeamSendOwnershipInvitation v-if="hasTeamPermission('team.owner.transfer')" />
<TeamDelete :team v-if="hasTeamPermission('team.delete')" />
```

## Commands you will need

| Purpose | Command | Expected on success |
| --- | --- | --- |
| Focused team tests | `php artisan test --compact tests/Feature/Teams/TeamMemberManagementTest.php tests/Feature/Teams/TeamInvitationTest.php tests/Feature/Teams/TeamDeletionTest.php tests/Feature/Billing/BillingSettingsTest.php` | exit 0 |
| Full PHP tests | `php artisan test --compact` | exit 0 |
| Typecheck | `npm run types:check` | exit 0 |
| Static/format | `vendor/bin/phpstan analyse --memory-limit=2G --no-progress && vendor/bin/pint --test --format agent` | exit 0 |

## Suggested executor toolkit

- Use `laravel-best-practices` for authorization and enum changes.
- Use `pest-testing` for feature test additions.

## Scope

**In scope**:

- `app/Enums/Teams/RoleEnum.php`
- Tests under `tests/Feature/Teams`
- `tests/Feature/Billing/BillingSettingsTest.php` if billing permission
  regression coverage is added there.

**Out of scope**:

- Changing global app admins (`users.is_admin`).
- Changing owner behavior; owners should still have all permissions.
- Redesigning the role system or adding new roles.
- Changing frontend layout, except if generated types require no-op updates.

## Git workflow

- Branch: `advisor/004-align-admin-role-permissions`
- Commit message: `Align team admin permissions`
- Do not push unless instructed.

## Steps

### Step 1: Add a direct role permission regression test

Add or update a focused test that asserts:

```php
expect(RoleEnum::ADMIN->permissions())->toEqualCanonicalizing(TeamMemberPermissionEnum::cases());
expect(RoleEnum::EDITOR->permissions())->toContain(TeamMemberPermissionEnum::TEAM_VIEW);
```

This can live in a new `tests/Feature/Teams/TeamRolePermissionTest.php` or
inside an existing team permissions test file if a better local pattern exists.

**Verify**:
`php artisan test --compact tests/Feature/Teams/TeamRolePermissionTest.php`
Expected before Step 2: the admin assertion fails.

### Step 2: Grant the admin role every team permission

In `RoleEnum::permissions()`, return every `TeamMemberPermissionEnum` case for
`self::ADMIN`:

```php
self::ADMIN => TeamMemberPermissionEnum::cases(),
```

Keep `EDITOR` as view-only unless the maintainer explicitly asks for editor
permission changes.

Do not change the `ADMIN` description; after this step it becomes true.

**Verify**:
`php artisan test --compact tests/Feature/Teams/TeamRolePermissionTest.php`
Expected: pass.

### Step 3: Add feature coverage for admin member actions

Add focused tests proving an admin member can perform at least these actions:

- Invite a team member.
- Update another member's role.
- Remove another member.
- View billing settings.

Use existing test files as patterns:

- `tests/Feature/Teams/TeamInvitationTest.php`
- `tests/Feature/Teams/TeamMemberManagementTest.php`
- `tests/Feature/Billing/BillingSettingsTest.php`

Do not test every permission through expensive Stripe checkout flows unless the
existing `tests/TestCase.php` Cashier fake makes it cheap.

**Verify**:
`php artisan test --compact tests/Feature/Teams/TeamInvitationTest.php tests/Feature/Teams/TeamMemberManagementTest.php tests/Feature/Billing/BillingSettingsTest.php`
Expected: all pass.

### Step 4: Verify frontend permission payload remains typed

The frontend uses `App.Enums.Teams.TeamMemberPermissionEnum` and
`useTeamPermissions()`. No frontend code should need to change because the
permission values are already enum cases.

Run:

```bash
npm run types:check
npm run lint:check
```

Expected: both exit 0.

### Step 5: Run broad checks

Run:

```bash
php artisan test --compact
vendor/bin/phpstan analyse --memory-limit=2G --no-progress
vendor/bin/pint --test --format agent
```

Expected: all exit 0.

## Test plan

Add one direct enum test and at least three feature-level authorization tests
for admin members. Continue Pest style with `RefreshDatabase` and existing
factory patterns.

## Done criteria

- [ ] `RoleEnum::ADMIN->permissions()` equals all `TeamMemberPermissionEnum`
  cases.
- [ ] Admin member feature tests cover invitation, member management, and
  billing access.
- [ ] Editor role behavior is unchanged unless explicitly requested.
- [ ] Full PHP tests, frontend typecheck/lint, Larastan, and Pint check pass.

## STOP conditions

- A maintainer says team admin should not have owner-only capabilities. If so,
  stop and rewrite the plan around renaming/redescribing the role instead.
- Granting all permissions causes billing or delete tests to expose a broader
  product-policy conflict.
- Generated TypeScript output is broken by local type generation drift. That is
  plan 005; do not solve it inside this plan unless already completed.

## Maintenance notes

When adding a new `TeamMemberPermissionEnum`, remember that `ADMIN` will receive
it automatically. Reviewers should check whether a new permission is truly safe
for admin members before adding it.

