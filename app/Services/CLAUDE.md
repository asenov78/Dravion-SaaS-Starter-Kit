# app/Services

Business logic layer. Services are stateless (static methods preferred) or injected via the container. Controllers and middleware delegate domain work here — never put this logic directly in a controller.

## Files

| File | Purpose |
|---|---|
| `ActivityLogger.php` | Wrapper around Spatie Activity Log. Checks `Setting::get('activity_log_{category}')` before logging. Accepts optional `$descKey` + `$descParams` for translatable log descriptions stored in `properties`. |
| `AvatarService.php` | Resizes and stores user avatars. Uses GD (not Intervention), converts everything to JPEG 85%, max 200×200px. Deletes old avatar from `public` disk before saving the new one. Returns the storage path (relative to `public` disk). |
| `LangKeyExtractor.php` | Reads `lang/{locale}/*.php` files and returns flat dot-notation keys (e.g. `auth.failed`). Used by the Language admin to seed/sync `language_lines`. |
| `LicenseService.php` | Reads and writes the HMAC-signed `storage/license.cache` file. `isValid()` is the single source of truth: `DEV-*` keys pass on local/test/dev domains only; production keys require a valid signed cache. Cache is written by `LicenseCheck` middleware. |
| `UpdaterService.php` | Fetches GitHub releases via API, compares semver against `config('dravion.version')`, and applies updates: puts app in maintenance, downloads ZIP, extracts, copies non-protected files, runs migrations, clears caches. |

## Conventions

- Keep services **stateless** where possible. Use `static` methods for pure utilities (`ActivityLogger`, `AvatarService`, `LangKeyExtractor`, `LicenseService`).
- `UpdaterService` is the exception — inject it via the container (it has no static state but is more complex).
- All user-visible strings produced by services must go through `__()` in the caller (controller/view), not in the service itself.
- Never flash session data or return HTTP responses from a service — return plain values/arrays/booleans.
- Activity logging categories must match the `activity_log_{category}` keys registered in `SettingsController::index()` and `Setting::setMany()`. Current categories: `auth`, `users`, `profile`, `settings`.

## Gotchas

- `AvatarService` requires the GD extension with JPEG support (`imagecreatefromstring`, `imagejpeg`). Do not switch to Intervention Image without updating this file and the composer deps.
- `LicenseService::isValid()` is **pessimistic** on missing cache — returns `false`. The cache is refreshed by `LicenseCheck` middleware on the next web request.
- `LicenseService::writeCache()` derives its HMAC key from `APP_KEY`. If `APP_KEY` changes, the cache is invalidated automatically (signature mismatch → `null`).
- `UpdaterService` skips paths listed in `config('updater.protected_paths')`. Always keep `.env`, `storage/`, and `config/dravion.php` in that list.
- `LangKeyExtractor` requires `lang/{locale}/` PHP files — it does not read JSON translation files.
