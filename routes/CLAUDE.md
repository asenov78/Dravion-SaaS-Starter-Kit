# routes/ — CLAUDE.md

Dravion SaaS Starter Kit · Laravel 13 route definitions.

## Files

| File | Purpose |
|---|---|
| `web.php` | All HTTP routes (web middleware group applied globally by bootstrap) |
| `console.php` | Artisan schedule / closure commands |

## Route groups and middleware

### Public
```
GET /          → HomeController@index (public website home, checks install.lock)
GET /p/{slug}  → HomeController@show  (dynamic CMS page by slug)
```

### Installer
```
middleware: InstallGuard (blocks after install.lock exists)
prefix:     /install
name:       install.*
```
`InstallGuard` redirects to `/` if `storage/install.lock` exists, preventing
re-running the installer on a live installation.

### Auth (guest only)
All auth form submissions are rate-limited. Never remove throttle middleware.

```
middleware: guest

GET  /login              login.show
POST /login              throttle:5,1   (5 attempts per minute)

GET  /register           register.show
POST /register           throttle:3,1   (3 attempts per minute)

GET  /forgot-password    password.request
POST /forgot-password    throttle:3,1   password.email

GET  /reset-password/{token}   password.reset
POST /reset-password           throttle:5,1   password.update
```

### Authenticated
```
middleware: auth

PUT    /profile/password     profile.password
PATCH  /profile/locale       profile.locale
POST   /logout               logout
GET    /locale/{code}        locale.switch
GET    /notifications                    notifications.index       (JSON bell feed)
POST   /notifications/read-all           notifications.read-all
POST   /notifications/{id}/read          notifications.read

GET    /email/verify                     verification.notice
GET    /email/verify/{id}/{hash}         verification.verify      (signed)
POST   /email/verification-notification  verification.send
```

### Auth + Verified
```
middleware: auth, verified

GET    /dashboard            dashboard
GET    /sessions                    sessions.index
POST   /sessions/logout-others      sessions.logout-others
GET    /api-tokens           api-tokens.index
POST   /api-tokens           api-tokens.store
DELETE /api-tokens           api-tokens.destroy-all
DELETE /api-tokens/{id}      api-tokens.destroy
```

### Admin
```
middleware: ['auth', 'role:admin|manager|editor', 'license.check']
prefix:     /admin
name:       admin.*
```

Fine-grained permission gates are added per-route with `can:` middleware:

| Capability | Middleware |
|---|---|
| View users list | `can:view users` |
| Create users | `can:create users` |
| Edit users | `can:edit users` |
| Suspend/activate users | `can:suspend users` |
| Delete / restore users | `can:delete users` |
| View settings | `can:view settings` |
| Edit settings | `can:edit settings` |
| View activity log | `can:view activity log` |

Certain sections are additionally restricted to `role:admin` only:
- `/admin/roles/*` — role & permission management
- `/admin/updates/*` — self-updater
- `/admin/license` GET is accessible to all admin roles; POST/DELETE are not
  separately guarded but the page itself checks role in the view.

### UI Showcase
```
middleware: admin group (auth + role + license)
prefix:     /admin/ui
name:       admin.ui.*
```
Pages: ecommerce, form-elements, tables, alerts, avatars, badges, buttons,
images, videos, calendar, bar-chart, line-chart, blank, profile.

## Conventions

### Naming
All admin routes follow `admin.[resource].[action]` (standard Laravel resource naming).
Example: `admin.users.index`, `admin.users.edit`, `admin.users.destroy`.

UI showcase routes: `admin.ui.[page]`.

### Adding new admin routes
1. Add the route inside the existing `['auth', 'role:admin|manager|editor', 'license.check']` group.
2. Apply the most specific `can:` middleware gate available.
3. Name the route `admin.[resource].[action]`.
4. Add a nav entry in the sidebar partial using `__('nav.key')`.

### Rate limiting
Never remove `throttle:N,M` from auth POST routes.
- Login: `throttle:5,1`
- Register: `throttle:3,1`
- Forgot-password: `throttle:3,1`
- Reset-password: `throttle:5,1`

## Gotchas
- The locale switcher (`locale.switch`) requires `auth` middleware — it is not
  available to guests.
- `/logout` is POST only (CSRF-protected) — do not change it to GET.
- The root `/` route checks for `install.lock` at runtime; do not cache this
  route via `php artisan route:cache` during development.
- `console.php` uses the `Schedule` facade — task scheduling is configured there,
  not in a separate kernel file (Laravel 13 style).
