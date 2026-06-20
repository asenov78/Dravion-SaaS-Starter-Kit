# app/Notifications

Laravel Notification classes. All use `Queueable`. Sent via `$user->notify(new XxxNotification(...))`.

## Files

| File | Channels | Triggered when | Recipient |
|---|---|---|---|
| `AccountActivatedNotification.php` | `mail` + `database` | Admin activates a suspended user | The user being activated |
| `AccountSuspendedNotification.php` | `mail` + `database` | Admin suspends a user | The user being suspended |
| `NewUserRegisteredNotification.php` | `database` only | A new user registers (via `RegisterController`) | Admin users |
| `UpdateInstalledNotification.php` | `database` only | An update is successfully installed | Admin users |

## `toArray()` shape (in-app bell)

All notifications return:

```php
[
    'title' => __('notifications.some_title'),
    'body'  => __('notifications.some_body', [...params]),
    'url'   => '/some/path',
]
```

`NotificationController::index()` reads these keys to build the JSON bell feed. Do not rename the keys.

## Adding a new notification

1. Create class implementing `via()`, `toMail()` (if mail), `toArray()`.
2. Add translation keys in `lang/en/notifications.php` and `lang/bg/notifications.php`.
3. Add translation keys in `lang/en/mail.php` / `lang/bg/mail.php` if sending email.
4. Send from the appropriate controller or observer — never from inside a model.

## Conventions

- All notifications use `Queueable` — queue driver defaults to `sync` in dev. Configure a real queue for production mail reliability.
- `toArray()` keys (`title`, `body`, `url`) are required — `NotificationController` depends on them.
- User-targeted notifications (`AccountActivated`, `AccountSuspended`) use both channels so the user gets an email and an in-app alert.
- Admin-targeted notifications (`NewUserRegistered`, `UpdateInstalled`) use `database` only to avoid spamming admin inboxes.
