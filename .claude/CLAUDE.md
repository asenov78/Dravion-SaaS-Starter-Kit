# Dravion SaaS Starter Kit — Claude Instructions

## Stack

- **Laravel 13**, PHP 8.3
- **Blade** templates with `<x-ui.*>` component system (38 shadcn/ui-parity components)
- **Alpine.js v3** — reactivity, flash store bridge, modals, sidebar collapse
- **Tailwind v4** — inline styles preferred over utility classes (project convention)
- **Spatie laravel-permission v8** + **spatie/laravel-activitylog v4**
- **MySQL** (production), **SQLite** (tests)
- **PHPUnit 12** — `composer test`

## Key Conventions

### Views & Styling
- Always use `<x-ui.*>` components (Button, Card, Badge, Toast, etc.) — never raw HTML for UI primitives.
- Use **inline styles**, not Tailwind utility classes. The project explicitly avoids mixed Tailwind/inline approaches.
- Alpine.js for all interactivity. Use `x-data`, `x-on`, `x-show`, Alpine stores (`$store`).
- Flash messages go through the Alpine store bridge: controllers dispatch to `session()->flash()`, JS reads from `window.__flash`.

### i18n — MANDATORY
- **Never hardcode UI strings in Blade files.** Always use `__('key')` or `@lang('key')`.
- Add keys to both `lang/en/*.php` and `lang/bg/*.php`.
- Run `php artisan lang:seed` after adding keys (if available).

### Controllers
- Admin controllers live in `App\Http\Controllers\Admin\`.
- Auth controllers in `App\Http\Controllers\Auth\`.
- `InstallController` handles the install wizard (`/install` route, self-locks after use).

### Routes
- All admin routes: `admin.*` named routes, under `auth` + `verified` middleware, prefixed `/admin`.
- Role/permission guards applied per-route (not just controller-level).
- Install routes are removed after install.

### Services
- `LicenseService` — validates license key against `DRAVION_LICENSE_SERVER`. Gates updater features.
- `UpdaterService` — fetches GitHub releases (all, not just latest), downloads, extracts, runs migrations. Protected paths: `.env`, `storage`, `vendor`, `node_modules`, `public/storage`, `public/build`.
- `AvatarService` — GD resize to 200×200px, stored in `storage/app/public/avatars/`.
- `ActivityLogger` — wraps spatie activitylog, called from model observers and controllers.

### Config
- `config/dravion.php` — version (`1.6.0`), license server URL, updates server URL, license key, licensed domain.
- `config/updater.php` — GitHub owner/repo/token, work dir (`storage/app/updates`), protected paths.

### Version
Current version: **1.8.0** (read from `config/dravion.php`).

---

## TDD Approach — Always Active

- **Write test first, then implementation.** Red → Green → Refactor.
- Tests live in `tests/Feature/` and `tests/Unit/`.
- SQLite in-memory for tests (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`).
- Run: `composer test` (clears config cache first).
- Every PR must keep all existing tests green. Add tests for every new feature and bug fix.
- Test naming: `test_it_does_the_thing()` snake_case, descriptive.

---

## Commit & Push Workflow

After every completed change:
1. `git add` relevant files.
2. `git commit -m "type: short description"` (conventional commits).
3. `git push` immediately — no asking, no waiting.

Version bump on every commit that changes behavior:
- Patch: `1.3.x` — bug fixes, minor UI tweaks.
- Minor: `1.x.0` — new features, new admin sections.
- Major: `x.0.0` — breaking changes, architectural rewrites.

Update `config/dravion.php` `version` field and add entry to `CHANGELOG.md` on every release commit.

---

## Caveman Mode — Always Active

Communicate in compressed, direct style. Drop filler words, articles, pleasantries. Keep technical precision. Example: "Add test, push" not "I'll go ahead and add a test for this and then push it to the repository."

---

## Security Constraints

- `GITHUB_TOKEN` must be a **fine-grained PAT** scoped to this repo only (`contents: read`).
- `DRAVION_LICENSE_KEY` and `DRAVION_LICENSE_SERVER` must live in `.env`, never committed.
- Install wizard (`/install`) self-destructs after use — never re-expose.
- Avatar uploads: validate MIME (GD re-encodes, eliminates embedded payloads). Max 2MB.
- Activity log: all admin actions logged with causer, subject, and properties diff.
- Permissions checked at route level AND controller level (double guard).

---

## Multi-Agent Usage

See `AGENTS.md` at project root for agent-type guidance.

---

## Session State

`.claude/STATE.md` — updated at the end of every session.

**At session start:** read `STATE.md` to resume context (pending tasks, current version, last commit).  
**At session end:** update `STATE.md` with: what changed, new pending tasks, current version, last commit SHA.
