# Plan 007: Resolve the high npm audit advisory for esbuild in build tooling

> **Executor instructions**: Follow this plan exactly. This plan changes npm
> dependency metadata, which is allowed because the operator selected this
> dependency finding. Run every verification command. Stop on STOP conditions.
> Update this plan's row in `plans/README.md` when complete.
>
> **Drift check (run first)**:
> `git diff --stat fcef216..HEAD -- package.json package-lock.json vite.config.ts resources/js`
> Compare the current package tree before proceeding if anything changed.

## Status

- **Priority**: P2
- **Effort**: M
- **Risk**: MED
- **Depends on**: none
- **Category**: dependencies
- **Planned at**: commit `fcef216`, 2026-06-13

## Why this matters

`npm audit --audit-level=high` currently fails because the installed tree uses
`esbuild@0.27.7`. The path is build tooling, not application runtime:
`@nuxt/ui -> @nuxt/fonts -> fontless -> esbuild`. CI does not currently run
`npm audit`, so the advisory can stay hidden until someone checks manually.
This plan updates the dependency tree or adds a focused override so audit,
build, and frontend checks pass together.

## Current state

Relevant package tree:

```text
@nuxt/ui@4.8.2
└─ @nuxt/fonts@0.14.0
   └─ fontless@0.2.1
      └─ esbuild@0.27.7
```

Current direct dependency:

```json
// package.json:40-44
"dependencies": {
    "@iconify-json/bi": "^1.2.7",
    "@inertiajs/vue3": "^3.4.0",
    "@nuxt/ui": "^4.8.2",
```

Lockfile evidence:

```json
// package-lock.json:1496-1506
"node_modules/@nuxt/fonts": {
    "version": "0.14.0",
    "dependencies": {
        "fontless": "^0.2.1",
```

```json
// package-lock.json:5157-5164
"node_modules/esbuild": {
    "version": "0.27.7",
    "bin": {
        "esbuild": "bin/esbuild"
```

```json
// package-lock.json:5663-5672
"node_modules/fontless": {
    "version": "0.2.1",
    "dependencies": {
        "esbuild": "^0.27.0",
```

Observed command output during audit:

- `composer audit --no-interaction`: no advisories.
- `npm audit --audit-level=high`: 4 high vulnerabilities, all through esbuild.
- `npm outdated --long @nuxt/ui @nuxt/fonts fontless esbuild`: esbuild latest
  was `0.28.1`; `vite` and `@unhead/bundler` wanted `0.28.1`, while `fontless`
  held `0.27.7`.

## Commands you will need

| Purpose | Command | Expected on success |
| --- | --- | --- |
| Inspect tree | `npm ls esbuild fontless @nuxt/fonts @nuxt/ui` | exit 0 |
| Audit | `npm audit --audit-level=high` | exit 0, no high advisories |
| Frontend build | `npm run build` | exit 0 |
| Frontend checks | `npm run test:frontend && npm run lint:check && npm run types:check && npm run format:check` | exit 0 |
| PHP checks | `php artisan test --compact && vendor/bin/phpstan analyse --memory-limit=2G --no-progress && vendor/bin/pint --test --format agent` | exit 0 |

## Suggested executor toolkit

- Use no special Laravel skill unless package changes break app code.
- If available, use a dependency-management or frontend tooling skill for npm
  override/upstream evaluation.

## Scope

**In scope**:

- `package.json`
- `package-lock.json`
- `.github/workflows/tests.yml` only if adding an audit CI gate after resolving
  the current advisory.

**Out of scope**:

- Changing PHP dependencies.
- Replacing Nuxt UI.
- Refactoring Vue components.
- Force-downgrading `@nuxt/ui` to satisfy `npm audit fix --force`.

## Git workflow

- Branch: `advisor/007-resolve-npm-audit-esbuild`
- Commit message: `Resolve high npm audit advisory`
- Do not push unless instructed.

## Steps

### Step 1: Reproduce the current advisory

Run:

```bash
npm ls esbuild fontless @nuxt/fonts @nuxt/ui
npm audit --audit-level=high
```

Expected before changes: `npm ls` shows `esbuild@0.27.7`; `npm audit` fails
with high advisories for esbuild.

If the advisory is already gone, stop and update the plan/index as stale rather
than making dependency changes.

### Step 2: Prefer a minimal esbuild override

Try the smallest dependency change first:

```json
"overrides": {
    "esbuild": "^0.28.1"
}
```

If `package.json` already has an `overrides` object, merge this key into it.
Then run:

```bash
npm install
```

This updates `package-lock.json`. Do not run `npm audit fix --force`; audit
reported that as a breaking/downgrade path.

**Verify**:
`npm ls esbuild fontless @nuxt/fonts @nuxt/ui`
Expected: all esbuild entries resolve to a non-vulnerable version, ideally
`0.28.1` or newer.

### Step 3: Run audit and build checks

Run:

```bash
npm audit --audit-level=high
npm run build
npm run test:frontend
npm run lint:check
npm run types:check
npm run format:check
```

Expected: all exit 0.

If `fontless` or Nuxt UI breaks under `esbuild@0.28.x`, remove the override and
investigate whether a newer `@nuxt/ui`, `@nuxt/fonts`, or `fontless` release
fixes the tree without an override. Do not accept a breaking downgrade.

### Step 4: Optionally add a CI audit gate

If Step 3 passes cleanly, add this CI step after `npm ci` in
`.github/workflows/tests.yml`:

```yaml
- name: Node Audit
  run: npm audit --audit-level=high
```

Only add the gate if it passes locally. If the maintainer prefers advisory
tracking outside CI, skip this step and note it in the final report.

**Verify**:
`npm audit --audit-level=high`
Expected: exit 0.

### Step 5: Run broad checks

Run:

```bash
php artisan test --compact
vendor/bin/phpstan analyse --memory-limit=2G --no-progress
vendor/bin/pint --test --format agent
```

Expected: all exit 0.

## Test plan

This is dependency/tooling work. Verification is command-based:

- `npm audit --audit-level=high` proves the advisory is resolved.
- `npm run build`, frontend tests, typecheck, lint, and format prove the forced
  esbuild version did not break the frontend toolchain.
- PHP tests/static checks ensure no unrelated app breakage.

## Done criteria

- [ ] `npm audit --audit-level=high` exits 0.
- [ ] `npm ls esbuild` shows no vulnerable `0.27.7` install.
- [ ] `npm run build` exits 0.
- [ ] Frontend tests, lint, typecheck, and format check pass.
- [ ] PHP tests, Larastan, and Pint check pass.
- [ ] Dependency changes are limited to npm metadata.

## STOP conditions

- The advisory is already resolved on the current branch.
- The override breaks Nuxt UI, Vite, or font generation.
- The only passing path is `npm audit fix --force` with a breaking downgrade.
- Resolving this requires broad package upgrades beyond the esbuild advisory.

## Maintenance notes

If an override is used, add a short comment in the PR description explaining
that it should be removed once the upstream `fontless` or `@nuxt/fonts` range
accepts the fixed esbuild version.

