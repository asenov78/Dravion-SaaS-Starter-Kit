# app/Support

Stateless utility classes. No framework bindings — pure static helpers and typed constants.

## Files

| File | Purpose |
|---|---|
| `DomainHelper.php` | Domain utilities: `isDevDomain(string)` (matches localhost/127.0.0.1/::1 and TLDs `.local`, `.test`, `.dev`), `fromAppUrl()` (extracts host from `APP_URL`), `maskKey(string)` (redacts license key for display, e.g. `DRV-****`). |
| `Settings.php` | Typed constants for every `Setting` key used in the app. Prevents bare-string typos when calling `Setting::get()` / `Setting::set()`. |

## Settings constants

```php
use App\Support\Settings;

Setting::get(Settings::APP_NAME);
Setting::get(Settings::REGISTRATION, '1');
Setting::get(Settings::ACTIVITY_LOG_AUTH);
```

Categories: general (`APP_NAME`, `APP_URL`, `TIMEZONE`, `DATE_FORMAT`, `DEFAULT_LANGUAGE`), mail (`MAIL_FROM`, `MAIL_FROM_NAME`, `MAIL_WELCOME`, `ADMIN_EMAIL`), auth (`REGISTRATION`, `MAINTENANCE`), branding (`LOGO`, `FOOTER_TEXT`, `FOOTER_COPYRIGHT`, `HEADER_TAGLINE`, `BROADCAST_BANNER`), activity log (`ACTIVITY_LOG_AUTH`, `ACTIVITY_LOG_USERS`, `ACTIVITY_LOG_PROFILE`, `ACTIVITY_LOG_SETTINGS`).

## Conventions

- All classes `final` — not meant to be extended.
- No constructor, no state — static methods only.
- When adding a new `Setting` key anywhere in the app, add it to `Settings.php` first.
