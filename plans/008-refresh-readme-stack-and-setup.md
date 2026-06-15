# Plan 008: Refresh README stack details and local verification commands

> **Executor instructions**: This is a docs-only plan. Do not touch source code.
> Run the verification commands and update this plan's row in
> `plans/README.md` when done.
>
> **Drift check (run first)**:
> `git diff --stat fcef216..HEAD -- README.md composer.json package.json .github/workflows/tests.yml`
> If README or stack metadata changed, compare the excerpts below before editing.

## Status

- **Priority**: P3
- **Effort**: S
- **Risk**: LOW
- **Depends on**: none
- **Category**: docs
- **Planned at**: commit `fcef216`, 2026-06-13

## Why this matters

The README is the first onboarding artifact, but it names outdated platform
versions and describes SSR as "with Nuxt" instead of this app's actual
Inertia/Vite setup using Nuxt UI components. That creates unnecessary confusion
for buyers, contributors, and agents executing future plans. The repo already
has exact setup and verification commands in `composer.json`, `package.json`,
and CI; the README should reflect those facts.

## Current state

Current stale README excerpt:

```markdown
<!-- README.md:21-30 -->
- **Robust & Modern Backend**: Built on the latest **Laravel 12** with **PHP 8.4**.
- **SEO-Ready with SSR**: **Server-Side Rendering** is ready out-of-the-box with Nuxt

## Tech Stack
- **Backend**: Laravel 12
- **Frontend**: Vue 3
- **UI Framework**: Nuxt UI 4
- **Backend/Frontend Bridge**: Inertia.js
```

Actual stack evidence:

```json
// composer.json:11-16
"require": {
    "php": "^8.5",
    "inertiajs/inertia-laravel": "^3.1.0",
    "laravel/cashier": "^16.5.3",
    "laravel/fortify": "^1.37.2",
    "laravel/framework": "^13.15",
```

```yaml
# .github/workflows/tests.yml:25-35
- name: Setup PHP
  with:
    php-version: 8.5
- name: Setup Node
  with:
    node-version: '25'
```

Current scripts:

```json
// package.json:5-14
"scripts": {
    "build": "vite build",
    "build:ssr": "vite build && vite build --ssr",
    "dev": "vite",
    "format": "prettier --write resources/",
    "format:check": "prettier --check resources/",
    "lint": "eslint . --fix",
    "lint:check": "eslint .",
    "test:frontend": "node --test tests/Frontend/*.test.mts",
    "types:check": "vue-tsc --noEmit"
}
```

## Commands you will need

| Purpose | Command | Expected on success |
| --- | --- | --- |
| Stale text check | `rg -n "Laravel 12|PHP 8\\.4|SSR.*Nuxt|with Nuxt" README.md` | no matches |
| Fresh text check | `rg -n "Laravel 13|PHP 8\\.5|Inertia|Nuxt UI" README.md` | relevant matches |
| Markdown sanity | `sed -n '1,120p' README.md` | readable, no broken table/list formatting |

## Scope

**In scope**:

- `README.md`

**Out of scope**:

- Source code.
- Package version changes.
- External documentation site updates.
- Marketing redesign.

## Git workflow

- Branch: `advisor/008-refresh-readme-stack`
- Commit message: `Refresh README stack details`
- Do not push unless instructed.

## Steps

### Step 1: Update stack version claims

In `README.md`, replace:

- Laravel 12 with Laravel 13.
- PHP 8.4 with PHP 8.5.
- "SSR with Nuxt" with wording that reflects Inertia SSR via Vite and Nuxt UI
  as the UI component framework.

Do not overpromise deployment details. Keep the README concise.

**Verify**:
`rg -n "Laravel 12|PHP 8\\.4|SSR.*Nuxt|with Nuxt" README.md`
Expected: no output.

### Step 2: Add a compact local command reference

Add a short section near "Getting Started" or after "Tech Stack" with the
repo-local commands:

```markdown
## Local Commands

- Install/setup: `composer run setup`
- Dev server: `composer run dev`
- Build: `npm run build`
- SSR build: `npm run build:ssr`
- Backend tests: `php artisan test --compact`
- Frontend tests: `npm run test:frontend`
- Typecheck: `npm run types:check`
- Lint: `npm run lint:check`
- Format check: `npm run format:check`
- Static analysis: `vendor/bin/phpstan analyse --memory-limit=2G`
```

If plan 005 has already changed `types:check` to regenerate types first, the
same command remains correct.

**Verify**:
`rg -n "composer run setup|php artisan test --compact|npm run types:check|vendor/bin/phpstan" README.md`
Expected: each command appears.

### Step 3: Keep README wording consistent with the product

Keep the existing SaaS starter-kit positioning. Do not add implementation
details that belong in the external docs site. Correct only inaccurate stack
and local-command content.

**Verify**:
`sed -n '1,140p' README.md`
Expected: readable Markdown with concise sections and no duplicate headings.

## Test plan

Docs-only. Use `rg` checks above as programmatic verification that stale version
claims are gone and local commands are present. No application tests are needed
unless source files are changed, which is out of scope.

## Done criteria

- [ ] README no longer mentions Laravel 12 or PHP 8.4.
- [ ] README correctly describes Laravel 13, PHP 8.5, Inertia, Vue 3, Nuxt UI,
  and Vite/Inertia SSR.
- [ ] README includes local verification commands matching repo scripts/CI.
- [ ] Only `README.md` and `plans/README.md` status are modified.

## STOP conditions

- The external documentation site is the intended single source of truth and
  the maintainer does not want local commands in README.
- Stack versions changed after this plan was written.
- You need to edit source code to make README claims true.

## Maintenance notes

When bumping Laravel, PHP, Node, or major frontend tooling, update README in the
same PR as the version bump so onboarding docs do not drift again.

