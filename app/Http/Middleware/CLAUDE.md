# app/Http/Middleware

Request lifecycle guards. Middleware here handles cross-cutting concerns: installation state, locale, maintenance mode, and license validation. All are registered in `bootstrap/app.php` or `routes/web.php`.

## Files

| File | Purpose |
|---|---|
| `InstallGuard.php` | Blocks access to install routes if `storage/install.lock` exists (aborts 404). Applied to the `install.*` route group. |
| `LicenseCheck.php` | Runs on every authenticated admin request. Reads the HMAC-signed license cache via `LicenseService::readCachePublic()`; if stale (> 24 h) or missing, pings the license server via cURL and calls `LicenseService::writeCache()`. Flashes `license_warning` to the session on invalid license — does **not** block access. |
| `MaintenanceMode.php` | Checks `Setting::get('maintenance')`. If `'1'`, aborts 503 unless the authenticated user has the `admin` role. Admins can always access the app during maintenance. |
| `SetLocale.php` | Sets `app()->locale` for every request. Priority: `session('locale')` → `$user->locale` → `config('app.locale', 'en')`. Must run early in the pipeline so `__()` calls return correct translations. |

## Conventions

- Middleware must not contain business logic — delegate to services (`LicenseService`, `Setting`).
- Return `$next($request)` unless the intent is to **hard-block** (abort/redirect). Soft warnings go in `session()->flash(...)`.
- New middleware should be added to `bootstrap/app.php` under the appropriate alias or group, not registered globally unless truly needed for every request.
- Use `$request->user()` (not `auth()->user()`) inside middleware — the session guard may not be booted yet when using `auth()->user()` in some pipeline positions.

## Gotchas

- `LicenseCheck` uses raw cURL (not Laravel's HTTP facade) for the server ping — this is intentional to avoid HTTP client boot overhead during every page load.
- `LicenseCheck` fails **open** on network errors: if the license server is unreachable, it writes `valid = true` and retries after 24 h. Do not change this to fail-closed without understanding the impact on server outages.
- `MaintenanceMode` checks Spatie roles (`hasRole('admin')`), so `HasRoles` must be on the `User` model and roles must be seeded before this middleware is useful.
- `InstallGuard` returns 404 (not 403) to avoid revealing the install route exists.
- `SetLocale` reads `$request->user()->locale` — this column must exist on the `users` table. If you add new locales, ensure they are seeded in the `languages` table.
