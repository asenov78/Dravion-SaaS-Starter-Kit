# app/Contracts

PHP interfaces that define the contracts for swappable services. Bound in `AppServiceProvider::register()`.

## Files

| File | Purpose |
|---|---|
| `LicenseServiceInterface.php` | Contract for `LicenseService`. Methods: `isValid()` (cache only, no network), `isValidLive()` (live ping, falls back to cache on `ConnectionException`), `verifyNow()` (always hits network), `activate(purchaseCode, domain)`, `readCachePublic()`, `writeCache(data)`. |
| `ActivityLoggerInterface.php` | Contract for `ActivityLogger` / `NullActivityLogger`. Methods: `log(category, event, description, subject, causer, descKey, descParams)` and `enabled(key)`. |

## Bindings (AppServiceProvider)

```php
$this->app->bind(LicenseServiceInterface::class, LicenseService::class);
$this->app->bind(ActivityLoggerInterface::class, ActivityLoggerService::class);
```

In tests, swap `ActivityLoggerInterface` → `NullActivityLogger` to skip DB dependency:

```php
$this->app->bind(ActivityLoggerInterface::class, NullActivityLogger::class);
```

## Conventions

- Every service that needs to be swappable (for testing or runtime substitution) gets an interface here.
- Interface methods must be fully documented with `@return` shapes for complex return types.
- Do not put implementation logic in interfaces — keep them lean.
