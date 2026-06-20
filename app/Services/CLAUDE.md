# app/Services

Business logic layer. Services implement interfaces from `app/Contracts/` and are injected via the container. Controllers, middleware, and observers delegate domain work here.

## Files

| File | Purpose |
|---|---|
| `ActivityLogger.php` | Implements `ActivityLoggerInterface`. Wraps Spatie Activity Log. Checks `Setting::get('activity_log_{category}')` before logging. Accepts optional `$descKey` + `$descParams` for translatable log descriptions stored in `properties`. Bound in `AppServiceProvider`. |
| `NullActivityLogger.php` | Implements `ActivityLoggerInterface`. No-op — all methods are empty. Swap in for tests to avoid DB/logging dependency. |
| `AvatarService.php` | Resizes and stores user avatars. Uses GD (not Intervention), converts everything to JPEG 85%, max 200×200px. Null-guards `imagecreatefromstring()` return. Deletes old avatar from `public` disk before saving. Returns the storage path (relative to `public` disk). |
| `LangKeyExtractor.php` | Reads `lang/{locale}/*.php` files and returns flat dot-notation keys (e.g. `auth.failed`). Used by the Language admin to seed/sync `language_lines`. |
| `LicenseService.php` | Implements `LicenseServiceInterface`. Reads/writes the HMAC-signed `storage/license.cache`. `isValid()` — fast cache-only check. `isValidLive()` — live ping, falls back to cache on `ConnectionException` (fail-open). `verifyNow()` — always hits network. DEV-* keys pass on local/test/dev domains only (via `DomainHelper`). |
| `UpdaterService.php` | Thin orchestrator. Fetches GitHub releases; delegates download to `Updater/ReleaseDownloader`, install to `Updater/ReleaseInstaller`, history to `Updater/UpdateHistory`. See `Updater/CLAUDE.md`. |

## Sub-services

`app/Services/Updater/` — `ReleaseDownloader`, `ReleaseInstaller`, `UpdateHistory`. See `Updater/CLAUDE.md`.

## Conventions

- Services implement interfaces from `app/Contracts/` — always inject via the interface, not the concrete class.
- `ActivityLogger` and `LicenseService` are injected (not static) — use the `ActivityLogger` **Facade** (`app/Facades/`) for call sites that can't inject.
- `NullActivityLogger` is for tests only — never bind it in production.
- All user-visible strings go through `__()` in the caller (controller/view), not in the service.
- Never flash session data or return HTTP responses from a service — return plain values/arrays/booleans.
- Activity logging categories: `auth`, `users`, `profile`, `settings` — match `activity_log_{category}` Setting keys.

## Gotchas

- `AvatarService` requires GD with JPEG support. Do not switch to Intervention Image without updating composer deps.
- `LicenseService::isValid()` is **pessimistic** on missing cache — returns `false`. Cache refreshed by `LicenseCheck` middleware.
- `LicenseService::writeCache()` HMAC key derived from `APP_KEY` — changing `APP_KEY` invalidates the cache.
- `LicenseService::isValidLive()` fails **open** on `ConnectionException` — unreachable license server does not revoke the license.
- `LangKeyExtractor` only reads PHP lang files — ignores JSON translation files.
- `UpdaterService::install()` must NOT be called concurrently — `UpdateController` enforces a sequential install rule via `cache()->lock()`.
