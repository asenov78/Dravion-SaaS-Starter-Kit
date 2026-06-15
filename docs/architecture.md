# Architecture — Dravion SaaS Starter Kit

## Version: 1.3.1

---

## Overview

Multi-tenant-ready Laravel 13 SaaS admin starter. Single-tenant by default (one admin org), with Spatie role/permission for fine-grained access control.

---

## Directory Structure (non-standard parts)

```
app/
  Http/Controllers/
    Admin/           — all admin panel controllers
    Auth/            — login, register, password reset
    InstallController.php   — one-time install wizard
    LocaleController.php    — EN/BG locale switcher
  Services/
    LicenseService.php      — license validation + caching
    UpdaterService.php      — GitHub release fetch, download, install
    AvatarService.php       — GD image resize (200x200)
    ActivityLogger.php      — wraps spatie activitylog
    LangKeyExtractor.php    — dev tool: extract i18n keys from Blade

config/
  dravion.php    — version, license server, updates server
  updater.php    — GitHub owner/repo/token, protected paths

resources/views/
  components/ui/   — 38 shadcn/ui-parity Blade components
  admin/           — admin panel views (dashboard, users, roles, settings, etc.)
  auth/            — login, register views
  install/         — installer wizard steps

lang/
  en/   — English strings
  bg/   — Bulgarian strings
```

---

## Auth Flow

1. Guest hits `/login` → `Auth\LoginController`
2. On success → redirect to `/admin/dashboard`
3. All admin routes guarded by `auth` + `verified` middleware
4. Role/permission checked at route level (`can:` middleware) AND controller (`$this->authorize()` or `Gate::check()`)
5. Spatie roles: `super-admin`, `admin`, `manager` (seeded). Custom roles via UI.

---

## License System

`LicenseService` validates the license key by POSTing to `DRAVION_LICENSE_SERVER` with `license_key` + `domain`. Response is cached in the DB settings table for 24h to avoid hammering the server.

`UpdaterService` calls `LicenseService::isValid()` before allowing download/install. The Updates page shows available releases regardless of license (latest version always visible).

---

## Self-Updater Flow

1. Admin navigates to `/admin/updates`
2. `UpdateController::index()` calls `UpdaterService::getAvailableReleases()` — fetches full GitHub releases list
3. Releases newer than `config('dravion.version')` shown with changelog, newest first, "latest" badge on top
4. "Install" button → `UpdateController::install(POST)`
5. License check → puts app in maintenance mode → downloads ZIP to `storage/app/updates/` → extracts, skipping protected paths → runs `php artisan migrate --force` → clears caches → takes app out of maintenance mode
6. Version in `config/dravion.php` is updated by the release ZIP (the new version ships its own config)

---

## Settings System

Key-value store in `settings` DB table. `Setting::get('key', 'default')` / `Setting::set('key', 'value')`.

Current settings keys:
- `app_name`, `app_logo`
- `mail_driver`, `mail_host`, `mail_port`, `mail_username`, `mail_password`, `mail_from_address`, `mail_from_name`
- `welcome_email_enabled`

---

## Flash / Alpine Bridge

Controllers flash via `session()->flash('success', __('msg'))`. The admin layout reads `window.__flash` from a `<script>` block injected by the layout. Alpine store `$store.flash` picks this up and shows toast notifications.

---

## Activity Log

All significant admin actions logged via `spatie/laravel-activitylog`:
- User created/updated/deleted/restored/suspended/activated
- Role created/updated/deleted
- Settings changed
- Avatar uploaded
- License actions

Viewable at Admin → Activity Log (paginated, with causer avatar and property diff tooltips).

---

## UI Component System

38 Blade components in `resources/views/components/ui/`. Mirrors shadcn/ui API (props-based). Key components:

| Component | Usage |
|-----------|-------|
| `<x-ui.button>` | `variant`, `size`, `tag`, `href` props |
| `<x-ui.card>` | Container with header/body/footer slots |
| `<x-ui.badge>` | `variant` prop |
| `<x-ui.toast>` | Driven by Alpine flash store |
| `<x-ui.alert-dialog>` | Confirm modal, `method` prop (DELETE/PATCH/etc.) |
| `<x-ui.input>` | Styled input, `<x-ui.label>` companion |

Styling: **inline styles only**. No Tailwind utility classes in views.

---

## i18n

Two locales: `en` (default), `bg`. All UI strings via `__('key')`. Locale switched at `/locale/{en|bg}` (stored in session).
