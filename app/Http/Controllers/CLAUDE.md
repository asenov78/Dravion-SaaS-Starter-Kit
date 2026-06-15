# app/Http/Controllers

HTTP layer only. Controllers validate input, call services/models, and return views or redirects. No business logic lives here.

## Structure

```
Controllers/
├── Controller.php              # Base class (extends Laravel's base)
├── ApiTokenController.php      # Sanctum personal access tokens (list/create/revoke)
├── HomeController.php          # Public website: index() + show($slug) for CMS pages
├── SessionController.php       # Active sessions list + logout other devices (password confirm)
├── LocaleController.php        # Switches session locale
├── NotificationController.php  # In-app notification bell feed + mark read/all
├── InstallController.php       # Multi-step installer wizard
├── Admin/
│   ├── ActivityController.php  # Activity log viewer
│   ├── DashboardController.php # Admin dashboard + system health
│   ├── GlobalSearchController.php # 3-char autosearch JSON endpoint
│   ├── LanguageController.php  # Language & translation management
│   ├── LicenseController.php   # License key activation/removal
│   ├── PagesController.php     # CMS pages CRUD (public website pages)
│   ├── ProfileController.php   # Admin's own profile
│   ├── RoleController.php      # Spatie roles + permission matrix
│   ├── SettingsController.php  # App-wide settings + SMTP test
│   ├── UpdateController.php    # GitHub-based self-updater UI
│   └── UserController.php      # User management
└── Auth/
    ├── ForgotPasswordController.php
    ├── LoginController.php
    ├── PasswordController.php
    ├── RegisterController.php
    └── ResetPasswordController.php
```

## Key Controller Notes

### InstallController
- Multi-step wizard: `requirements → database → admin → license → finish`.
- State is carried between steps via session keys `install_db`, `install_admin`, `install_license`.
- `handleFinish` writes `.env`, hot-swaps the DB connection at runtime, runs migrations, seeds roles, creates the admin user, and writes `storage/install.lock`.
- Guarded by `InstallGuard` middleware — 404s if `install.lock` exists.

### Admin/DashboardController
- `clearCache()` also deletes `storage/license.cache` — triggers a fresh license validation on next request.
- `systemHealth()` returns raw PHP/server info for the dashboard widget; reads SQLite DB size if applicable.

### Admin/UserController
- Full CRUD + soft-delete restore + suspend/activate lifecycle.
- `bulk()` handles bulk suspend/activate/delete — self-exclusion built in, ids come from checkbox form.
- Bulk delete uses `->get()->each->delete()` (not `->each->delete()` directly on builder — SoftDeletes requires model instances).
- Every mutating action calls `ActivityLogger::log()` with a translatable `descKey`.
- Avatar upload delegates to `AvatarService::store()`.
- `store()` sends a `WelcomeMail` with a password-reset link when `Setting::get('mail_welcome') === '1'`.
- Soft delete: `destroy()` soft-deletes (does not permanently remove). Restore via `restore($id)`.
- `suspend()` / `activate()` check `wantsJson()` for AJAX compatibility (Alpine.js inline actions).

### Admin/RoleController
- `admin` role cannot be deleted or renamed (hard-coded 403 guard in both `destroy()` and `update()`).
- `syncPermissions()` accepts a matrix `[role_id => [permission_id, ...]]` — replaces all non-admin role permissions in one POST.
- `update()` renames a role — route `/roles/{role}` must come AFTER `/roles/permissions` in web.php to avoid route collision.

### Admin/SettingsController
- All settings stored via `Setting::setMany()`.
- Boolean settings stored as `'1'`/`'0'` strings.
- `smtpTest()` returns JSON `{ok, message}` — consumed by Alpine.js on the settings page.

### Admin/LicenseController
- `update()` posts to `dravion.license_server` to activate a purchase code → receives a `license_key` → writes it to `.env` via regex replace.
- `remove()` clears the key from `.env` and deletes `storage/license.cache`.
- Skips `.env` writes in `testing` environment.

### Admin/UpdateController
- Uses `UpdaterService` to fetch GitHub releases and apply updates.
- ZIP URL validated with `GitHubZipUrl` rule before download.

### ApiTokenController
- `index()` passes `$tokens` (user's tokens) + `$new_token` (session flash — plaintext shown once).
- `store()` validates name, calls `createToken()`, flashes plaintext to session, redirects.
- `destroy($id)` loads `PersonalAccessToken::findOrFail()`, checks `tokenable_id === auth()->id()`, deletes.
- `destroyAll()` calls `$user->tokens()->delete()`.
- Route: `GET/POST/DELETE /api-tokens` — accessible to all `auth + verified` users, NOT admin-only.

### NotificationController
- `index()` returns JSON: `{unread_count, notifications[]}` — consumed by Alpine.js bell in header.
- Each notification item: `{id, title, body, url, read, created}` — keys come from `toArray()` on each Notification class.
- `markRead($id)` — checks `notifiable_id === auth()->id()` before marking.
- `markAllRead()` — calls `$user->unreadNotifications->markAsRead()`.
- Routes at `/notifications/*` — `auth` only (no `verified` — bell visible before email verification).

## Conventions

- All `Admin/` controllers require the `auth` middleware and Spatie `role:admin` (or equivalent permission check) — enforce this in `routes/web.php`, not inside the controller.
- Flash messages use `__('flash.*')` keys. Add new keys to `lang/en/flash.php` (and `lang/bg/flash.php`) before using them.
- JSON responses from admin actions follow `{success: bool}` or `{ok: bool, message: string}` — keep consistent.
- Pagination uses `->paginate(20)->withQueryString()` — always append `withQueryString()` to preserve filters.
- Use `Rule::unique('table')->ignore($model->id)` on update validations, not `unique:table,column,{id}` string syntax.
- `wantsJson()` branching (return JSON vs redirect) is used for actions triggered by Alpine.js — maintain this pattern for any new AJAX-capable actions.

## Gotchas

- `InstallController::writeEnv()` uses `addslashes()` on the DB password — it does NOT URL-encode or shell-escape. Passwords with backslashes could cause issues.
- `LicenseController::writeEnvKey()` uses a regex on the raw `.env` file content — concurrent requests during license activation could corrupt the file. Acceptable for low-traffic admin usage.
- `UserController::restore()` and `destroy()` double-check `hasRole('admin')` inside the method because they can be called via JSON (no route-level middleware guarantee in that context). Do not remove these checks.
- `Auth/RegisterController` respects `Setting::get('registration', '1')` — check this setting before assuming registration is open.
