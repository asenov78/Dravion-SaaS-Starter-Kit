# app/Facades

Laravel Facades that proxy to container-bound services via their interface.

## Files

| File | Facade accessor | Proxies to |
|---|---|---|
| `ActivityLogger.php` | `ActivityLoggerInterface::class` | `App\Services\ActivityLogger` (or `NullActivityLogger` in tests) |

## Usage

```php
use App\Facades\ActivityLogger;

ActivityLogger::log('users', 'created', "Created user {$user->name}", $user);
ActivityLogger::enabled('activity_log_users'); // bool
```

The Facade resolves through `ActivityLoggerInterface`, so swapping the binding in tests (`NullActivityLogger`) automatically silences all logging — no static mocking needed.

## Conventions

- Add a `@method` docblock for each interface method so IDEs autocomplete correctly.
- Facade accessor must be the **interface class name** (not the concrete service), to honour the DI binding.
- Do not call `ActivityLogger::log()` directly from Blade views — only from controllers, observers, and services.
