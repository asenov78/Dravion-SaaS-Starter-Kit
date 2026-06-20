# app/Providers

Laravel service providers. Registered in `bootstrap/providers.php`.

## Files

| File | Purpose |
|---|---|
| `AppServiceProvider.php` | Main provider: DI bindings, model observer registration, DB string length default, storage disk URL fix, symlink auto-creation. |

## AppServiceProvider responsibilities

### register()
- Extends `translation.loader` with `DatabaseLoader` (DB-driven translations override file-based ones).
- Binds `LicenseServiceInterface::class` → `LicenseService::class`.
- Binds `ActivityLoggerInterface::class` → `ActivityLoggerService::class`.

### boot()
- Registers `UserObserver` on `User`.
- Sets `Schema::defaultStringLength(191)` for MySQL utf8mb4 compatibility.
- Overrides `filesystems.disks.public.url` at runtime using `url('storage')` — fixes shared hosting where `APP_URL` env var lacks the subdirectory prefix.
- Creates missing `storage/` subdirectories on first boot (`framework/sessions`, `framework/cache/data`, `framework/views`, `logs`).
- Auto-creates or repairs the `public/storage` symlink (tries absolute, falls back to relative path for shared hosts).

## Gotchas

- The storage URL override only runs when NOT in console AND `HTTP_HOST` is set. Artisan commands use the raw `.env` value.
- The symlink repair deletes a broken symlink (exists but points to non-existent target) before recreating — needed on shared hosts after migration.
- Adding a new interface binding: add it in `register()`, NOT `boot()`. `boot()` is for side-effect setup that depends on the container being fully built.
