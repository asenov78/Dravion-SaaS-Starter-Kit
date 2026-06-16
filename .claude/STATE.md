# Session State — Dravion SaaS Starter Kit

> Updated: 2026-06-16 (session 7 end) | Version: 1.10.0

## Current State

- **Tests:** 493 total, 492 passing, 1 skipped (MySQL integration)
- **Branch:** main, up to date with origin
- **Last commit:** d0b82e4 — feat: v1.10.0 installer tests, shared hosting fixes, security patches
- **Release archive:** `C:\Users\p.karolev\Documents\Claude\dravion-v1.10.0.tar.gz` (4.11 MB)

## Completed This Session

1. **Installer tests** (38 tests):
   - `InstallGuardTest` — lock file blocks all routes (404), accessible without lock
   - `InstallFlowTest` — all 5 steps: GET views, POST validation, session flow, admin creation, lock, cleanup
   - Key fixes that emerged from tests:
     - `User#[Fillable]`: added `email_verified_at` — `firstOrCreate()` was silently dropping it
     - Test mock: `Artisan::shouldReceive('call')->andReturn(0)` + manual seeder run instead of `andCallOriginal()`

2. **Shared hosting installer hardening:**
   - `bootstrapEnv()`: auto-creates `.env` from `.env.example`, forces `SESSION_DRIVER=file` before DB exists
   - `seedDefaultLanguage()`: inserts default `en` row into `languages` on finish
   - Storage dirs auto-created on finish (framework/sessions, cache/data, views, logs, app)
   - `storage:link` attempt on finish (non-fatal)
   - `writeEnv()`: full MAIL_* defaults, APP_LOCALE, APP_FALLBACK_LOCALE, FILESYSTEM_DISK=local

3. **CSO Audit + Security fixes:**
   - `RegisterController::store()`: checks `Setting::get('registration')` — prevents bypass via direct POST
   - Language routes: `can:manage languages` permission middleware (admin-only)
   - `RolesAndPermissionsSeeder`: added `manage languages` permission
   - Lang: `auth.registration_disabled` in en + bg

## Pending / Next Steps

- None critical. All TODO tasks complete (#5 ionCube — optional, separate effort).
- CSO medium finding not fixed: CMS page `{!! $content !!}` — intentional rich text. Could add HTMLPurifier later.
- CSO medium finding not fixed: 2FA session fixation window (pre-2FA session ID not rotated at challenge entry) — low exploitability, good hardening candidate.

## Architecture Snapshot

```
Laravel 13 / PHP 8.3 / Tailwind v4 / Alpine.js v3 / Spatie v8 / PHPUnit 12
Public:   GET / → HomeController; GET /p/{slug} → HomeController@show
Auth:     LoginController (suspend check, failed-login logging, 2FA gate)
          TwoFactorController (TOTP setup/confirm/disable/challenge/verify)
          RegisterController (registration setting gate)
          MustVerifyEmail enforced
Admin:    UserController, PagesController, RoleController, SettingsController
          LicenseController, UpdateController, ActivityController, GlobalSearchController
          LanguageController (requires can:manage languages — admin only)
Contracts: LicenseServiceInterface → LicenseService (DI via AppServiceProvider)
Services: LicenseService (activate + HMAC cache), UpdaterService (GitHub ZIP)
          AvatarService (GD), ActivityLogger (Spatie wrapper)
          EnvWriter — atomic .env writes with flock() + proper value escaping
Observers: UserObserver — created/updated/deleted/restored → ActivityLogger
Middleware: InstallGuard, LicenseCheck (DI), SetLocale, MaintenanceMode
Roles:    Spatie: admin/manager/editor/user + fine-grained can: gates per route
i18n:     DB-driven + lang/en/ + lang/bg/ (17 files)
Install:  5-step wizard → bootstrapEnv → write .env → migrate → seed → admin → install.lock
          Requirements: PHP 8.3, PDO, MySQL, Mbstring, OpenSSL, JSON, BCMath, cURL, GD
Tests:    SQLite in-memory, 492 passing (493 total, 1 MySQL skip), 46 test files
2FA:      /profile/two-factor (setup/manage), /two-factor/challenge (login gate)
Perms:    manage languages (new, admin-only), seeded in RolesAndPermissionsSeeder
```

## Standing Instructions (always active)

- caveman + tdd — active every prompt
- Multi-agent — Explore/Plan/CSO per task
- Push after every completed change
- End every task with "Готов съм!"
- All UI strings via `__()`, never hardcoded in Blade
- Update STATE.md at session end
