# app/Observers

Eloquent model observers. Registered in `AppServiceProvider::boot()`.

## Files

| File | Model | Purpose |
|---|---|---|
| `UserObserver.php` | `User` | Logs `created`, `updated`, `deleted`, `restored` events via `ActivityLogger` facade. |

## UserObserver behaviour

- All methods **skip** when `auth()->check()` is false (seeding, install wizard, console).
- `updated()` only logs if a meaningful field changed: `name`, `email`, `status`, `bio`, `phone`, `country`, `city_state`, `postal_code`, `tax_id`, `facebook`, `x_url`, `linkedin`, `instagram`. Internal fields (`email_verified_at`, `remember_token`, etc.) are ignored.
- Logs to category `'users'` — controlled by `Setting::get('activity_log_users')`.

## Registration

```php
// AppServiceProvider::boot()
User::observe(UserObserver::class);
```

## Conventions

- Observers must never send notifications or emails — delegate to dedicated Notification classes.
- Use `ActivityLogger` facade (not the service directly) so tests can swap to `NullActivityLogger`.
- Translatable description keys follow pattern `activity.log.{event}` (e.g. `activity.log.user_created`).
