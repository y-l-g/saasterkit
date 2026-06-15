# Plan 005: Stabilize generated TypeScript types and remove machine-specific output

> **Executor instructions**: Follow the plan step by step. Run every command and
> stop on STOP conditions. Update this plan's row in `plans/README.md` when
> done.
>
> **Drift check (run first)**:
> `git diff --stat fcef216..HEAD -- app/Providers/TypeScriptTransformerServiceProvider.php config/typescript-transformer.php package.json package-lock.json .gitignore resources/js/generated resources/js/types`
> If in-scope files changed, compare against the excerpts below.

## Status

- **Priority**: P2
- **Effort**: M
- **Risk**: MED
- **Depends on**: none
- **Category**: dx
- **Planned at**: commit `fcef216`, 2026-06-13

## Why this matters

The repo tracks generated TypeScript declarations under a path containing this
workstation's absolute directory:
`resources/js/generated/home/youenn/Documents/Github/saasterkit/...`. The
manifest also stores an absolute path. This makes fresh clones and other
machines depend on a local path artifact instead of a deterministic generation
flow. The repo should generate app types into a stable ignored path and make the
typecheck command regenerate them before `vue-tsc`.

## Current state

Relevant files:

- `config/typescript-transformer.php` says output should be
  `resources/js/types/generated.d.ts`.
- `app/Providers/TypeScriptTransformerServiceProvider.php` overrides the writer
  to `resources/types/generated.d.ts`.
- Git tracks generated files under `resources/js/generated/...`.
- `.gitignore` already ignores `/resources/js/types/generated.d.ts`, but not the
  `resources/js/generated` manifest/artifact directory.
- `package.json` currently runs `vue-tsc --noEmit` directly.

Conflicting config excerpts:

```php
// config/typescript-transformer.php:62-66
'output_file' => resource_path('js/types/generated.d.ts'),
```

```php
// app/Providers/TypeScriptTransformerServiceProvider.php:25-27
->transformDirectories(app_path())
->writer(new GlobalNamespaceWriter(resource_path('types/generated.d.ts')))
->formatter(PrettierFormatter::class);
```

Tracked manifest excerpt:

```json
// resources/js/generated/typescript-transformer-manifest.json:1-3
{
    "\/home\/youenn\/Documents\/Github\/saasterkit\/resources\/types\/generated.d.ts": "083c46940b93c74d517e70939f174b90"
}
```

Tracked generated artifact:

```text
resources/js/generated/home/youenn/Documents/Github/saasterkit/resources/types/generated.d.ts
```

Current script:

```json
// package.json:13-14
"test:frontend": "node --test tests/Frontend/*.test.mts",
"types:check": "vue-tsc --noEmit"
```

## Commands you will need

| Purpose | Command | Expected on success |
| --- | --- | --- |
| Generate types | `php artisan typescript:transform --no-interaction` | exit 0, writes `resources/js/types/generated.d.ts` |
| Typecheck | `npm run types:check` | exit 0 |
| Tracked artifact check | `git ls-files resources/js/generated` | no output |
| Ignored output check | `git check-ignore -v resources/js/types/generated.d.ts resources/js/generated/typescript-transformer-manifest.json` | both ignored |
| Frontend checks | `npm run lint:check && npm run format:check && npm run test:frontend` | exit 0 |
| PHP checks | `php artisan test --compact && vendor/bin/phpstan analyse --memory-limit=2G --no-progress && vendor/bin/pint --test --format agent` | exit 0 |

## Suggested executor toolkit

- Use `wayfinder-development` only if route helpers become involved. This plan
  is about Spatie TypeScript Transformer, not Wayfinder.
- Use `laravel-best-practices` for provider/config consistency.

## Scope

**In scope**:

- `app/Providers/TypeScriptTransformerServiceProvider.php`
- `config/typescript-transformer.php`
- `package.json`
- `package-lock.json` only if npm updates the lockfile because scripts changed.
- `.gitignore`
- Remove tracked files under `resources/js/generated/**`.
- Generated `resources/js/types/generated.d.ts` should remain ignored, not
  committed.

**Out of scope**:

- Changing TypeScript transformer package versions.
- Changing Wayfinder route generation.
- Changing Laravel Data DTO shapes.
- Broad frontend refactors.

## Git workflow

- Branch: `advisor/005-stabilize-generated-types`
- Commit message: `Stabilize generated TypeScript types`
- Do not push unless instructed.

## Steps

### Step 1: Choose the canonical generated type path

Use `resources/js/types/generated.d.ts` as the canonical path because:

- `config/typescript-transformer.php` already points there.
- `tsconfig.json` includes `resources/js/**/*.d.ts`.
- `.gitignore` already ignores `/resources/js/types/generated.d.ts`.

Update `TypeScriptTransformerServiceProvider` so the writer uses the same path:

```php
->writer(new GlobalNamespaceWriter(resource_path('js/types/generated.d.ts')))
```

Keep the existing formatter unless it breaks generation.

**Verify**:
`php artisan typescript:transform --no-interaction`
Expected: exit 0 and `test -f resources/js/types/generated.d.ts` succeeds.

### Step 2: Ignore transformer cache/manifest output

Add ignore rules for generated transformer cache output if missing:

```gitignore
/resources/js/generated/
```

Do not ignore `resources/js/types/globals.d.ts` or
`resources/js/types/index.d.ts`; those are handwritten first-party types.

**Verify**:
`git check-ignore -v resources/js/generated/typescript-transformer-manifest.json resources/js/types/generated.d.ts`
Expected: both paths are ignored.

### Step 3: Remove machine-specific tracked artifacts

Remove tracked generated files under `resources/js/generated/**` from git.

Required result:

```bash
git ls-files resources/js/generated
```

prints no output after the change is staged/committed.

Do not delete or modify `resources/js/types/globals.d.ts` or
`resources/js/types/index.d.ts`.

**Verify**:
`git ls-files resources/js/generated`
Expected: no output.

### Step 4: Make typecheck regenerate types

Update `package.json` scripts so `npm run types:check` regenerates PHP-derived
types before running Vue typecheck. Use scripts like:

```json
"types:generate": "php artisan typescript:transform --no-interaction",
"types:check": "npm run types:generate && vue-tsc --noEmit"
```

If editing `package.json` changes `package-lock.json`, include the minimal
lockfile script metadata change. Do not change dependency versions.

**Verify**:
`npm run types:check`
Expected: transformer runs, then `vue-tsc --noEmit` exits 0.

### Step 5: Run full checks

Run:

```bash
npm run types:check
npm run lint:check
npm run format:check
npm run test:frontend
php artisan test --compact
vendor/bin/phpstan analyse --memory-limit=2G --no-progress
vendor/bin/pint --test --format agent
```

Expected: all exit 0.

## Test plan

This is a generated-artifact/tooling fix. The verification is command-based:
type generation plus `vue-tsc`, frontend checks, and PHP checks. No new feature
test is needed unless the executor changes DTO shapes, which is out of scope.

## Done criteria

- [ ] `TypeScriptTransformerServiceProvider` and config point to the same
  output path.
- [ ] `resources/js/generated/**` has no tracked files.
- [ ] `resources/js/types/generated.d.ts` is generated locally and ignored.
- [ ] `npm run types:check` works from a clean checkout after Composer and npm
  dependencies are installed.
- [ ] No dependency versions changed.

## STOP conditions

- `php artisan typescript:transform --no-interaction` cannot write to
  `resources/js/types/generated.d.ts`.
- `vue-tsc` still needs the machine-specific tracked file after generation.
- Fixing this appears to require changing package versions or DTO structures.

## Maintenance notes

Generated types should be reproducible, not workstation-specific. If CI later
adds a generated-artifact verification step, it should run
`npm run types:check` and fail if generated types cannot be produced.

