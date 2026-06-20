# Dravion SaaS Starter Kit — Claude Instructions

## MANDATORY: Read CLAUDE.md Before Every Action

**Before touching any file**, read the CLAUDE.md of every directory you'll modify:

```
app/Contracts/       app/Facades/         app/Http/Controllers/
app/Http/Middleware/  app/Models/          app/Notifications/
app/Observers/       app/Providers/       app/Rules/
app/Services/        app/Services/Updater/ app/Support/
config/              lang/                resources/views/
resources/views/admin/ resources/views/components/
routes/              tests/
```

Workflow on every prompt:
1. Identify which directories are affected
2. Read their CLAUDE.md files
3. Check `.claude/STATE.md` for current version + pending tasks
4. Then act

---

## Stack

- **Laravel 13**, PHP 8.3
- **Blade** templates with `<x-ui.*>` component system (38 shadcn/ui-parity components)
- **Alpine.js v3** — reactivity, flash store bridge, modals, sidebar collapse
- **Tailwind v4** — inline styles preferred over utility classes
- **Spatie laravel-permission v8** + **spatie/laravel-activitylog v4**
- **MySQL** (production), **SQLite** (tests)
- **PHPUnit 12** — `php artisan test`

## Architecture

- Services implement interfaces in `app/Contracts/` — inject via interface, never concrete class
- `ActivityLogger` Facade (`App\Facades\ActivityLogger`) for call sites that can't inject
- `LicenseService`: `isValid()` = cache-only (fast, UI); `isValidLive()` = live server ping (security gates)
- `UpdaterService` = thin orchestrator → `ReleaseDownloader` / `ReleaseInstaller` / `UpdateHistory`
- `Setting::get()` has request-scoped static cache — always call `Setting::flushCache()` in tests

## Key Conventions

### Views & Styling
- Always use `<x-ui.*>` components — never raw HTML for UI primitives
- Use **inline styles**, not Tailwind utility classes
- Alpine.js for all interactivity (`x-data`, `x-on`, `x-show`, `$store`)
- Flash messages: controllers → `session()->flash()` → Alpine `$store.flash`

### i18n — MANDATORY
- **Never hardcode UI strings in Blade.** Always `__('key')` or `@lang('key')`
- Add keys to both `lang/en/*.php` AND `lang/bg/*.php`
- Run `php artisan lang:seed` after adding keys

### Controllers
- Admin: `App\Http\Controllers\Admin\` — all return JSON or redirect, never business logic
- `install()` on `UpdateController` always returns `response()->json()` — never `abort()`
- All redirects in `LicenseController` go to `route('admin.license')`, never `back()`

### Routes
- Admin: `admin.*` named, `auth + role:admin|manager|editor + license.check`, prefix `/admin`
- User portal: `auth + verified`, no prefix — `GET /dashboard` (name: `dashboard`)
- Permission gates per-route with `can:` middleware

### Updates — Sequential Install Rule
**NEVER install multiple versions in parallel.** Each version installs sequentially oldest→newest.
Each POST `/admin/updates/install` handles exactly one version. Migrations run in order.

### Config
- `config/dravion.php` — version, license server URL, license key, licensed domain
- `config/updater.php` — GitHub owner/repo/token, work dir, protected paths
- Current version: read from `config/dravion.php`

---

## TDD Approach — Always Active

- **Write test first.** Red → Green → Refactor
- Tests: `tests/Feature/` and `tests/Unit/`
- SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`)
- Run: `php artisan test --no-coverage`
- Every change needs tests. Keep all existing tests green.
- `Setting::flushCache()` in `TestCase::setUp()` — required to prevent static cache bleed

---

## Commit & Push Workflow

After every completed change:
1. `git add` specific files (never `git add -A`)
2. `git commit -m "type: description"` (conventional commits)
3. `git push` — no asking, no waiting

Version bump on every behavioral change:
- Patch `1.x.y` → bug fixes, minor tweaks
- Minor `1.x.0` → new features
- Major `x.0.0` → breaking changes

Update `config/dravion.php` `version` + `CHANGELOG.md` on every release commit.

---

## Caveman Mode — Always Active

Direct, compressed. No filler. Technical precision kept.

---

## Security Constraints

- `GITHUB_TOKEN` — fine-grained PAT, this repo only, `contents: read`
- `DRAVION_LICENSE_KEY` + `DRAVION_LICENSE_SERVER` — `.env` only, never committed
- Install wizard (`/install`) self-destructs after use
- Avatar uploads: GD re-encode validates MIME. Max 2MB.
- All admin actions logged (causer, subject, diff)
- Permissions: route level AND controller level (double guard)

---

## Session State

`.claude/STATE.md` — read at session start, update at session end.

**Start:** read STATE.md → current version, pending tasks, last commit.
**End:** update STATE.md → what changed, new pending tasks, version, last commit SHA.
