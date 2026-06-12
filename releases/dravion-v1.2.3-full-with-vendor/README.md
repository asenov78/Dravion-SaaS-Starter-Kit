# Dravion SaaS Starter Kit

**v1.2.2** · Laravel 13 · PHP 8.2+ · Alpine.js · Spatie Permission · shadcn/ui-parity Blade components

Production-ready SaaS starter kit for shared hosting — dark admin panel, animated network background, installer wizard, RBAC, activity logging, 38 UI components.

---

## Features

### Admin Panel
- Linear/Quantix-style dark sidebar — glass backdrop, animated collapse, GENERAL/TOOLS/SUPPORT sections
- Topbar — search, Live data + System healthy pills, notification bell, user avatar/name
- Page header — title + per-page `$actions` slot (date range, filter, export)
- Floating layout — 8px gap from browser edges, `border-radius:12px` app shell
- Animated canvas background — 55 particles, dark blue + cyan network lines (`requestAnimationFrame`)
- Background on all pages — login, register, admin (via `<x-ui.net-bg>`)

### Authentication
- Login, Register, Logout
- Suspended user block
- Guest middleware on auth routes

### Role-Based Access Control (Spatie)
| Role | Access |
|---|---|
| `admin` | Full admin panel + all permissions |
| `manager` | Users + Activity |
| `editor` | View users |
| `user` | User dashboard only |

### User Management
- List, Create, Edit, Suspend, Activate
- Role assignment per user
- Avatar initials, status/role badges

### Settings
- Key-value DB store (`settings` table)
- `Setting::get()` / `Setting::set()` / `Setting::setMany()`
- Admin UI: app name, email, timezone, maintenance mode, registration toggle

### Activity Log (Spatie)
- Logs User model changes: `name`, `email`, `status` (dirty-only, no empty logs)
- Paginated table (30/page), causer avatars, log_name + causer filters

### Installer Wizard (`/install`)
| Step | Action |
|---|---|
| 1. Requirements | PHP ≥8.2, 9 extensions, writable paths |
| 2. Database | PDO connection test, stores in session |
| 3. Admin | name/email/password for super-admin |
| 4. License | purchase code |
| 5. Finish | writes `.env`, `migrate --force`, seeds roles, creates admin, writes `install.lock` |

After `storage/install.lock` exists → all `/install/*` routes return 404.

### UI Component Library — 38 components (`<x-ui.*>`)

| Category | Components |
|---|---|
| Core | `button` `badge` `card` `input` `alert` `label` `stat` |
| Form | `textarea` `checkbox` `select` `radio-group` `input-otp` |
| Layout | `separator` `aspect-ratio` `scroll-area` |
| Display | `avatar` `skeleton` `spinner` `progress` `breadcrumb` `kbd` `table` |
| Overlay | `dialog` `sheet` `drawer` `alert-dialog` `tooltip` `hover-card` `popover` |
| Navigation | `tabs` `accordion` `dropdown` `menubar` `navigation-menu` `context-menu` `pagination` |
| Interaction | `switch` `toggle` `toggle-group` `slider` `collapsible` |
| Feedback | `toast` |
| Background | `net-bg` |

---

## Routes

```
Public
  GET  /                           welcome page
  GET  /login                      login form
  POST /login                      authenticate
  GET  /register                   register form
  POST /register                   create account
  POST /logout                     sign out (auth)

Installer (blocked after install.lock)
  GET  /install                    → redirect to /install/requirements
  GET  /install/{step}             show step
  POST /install/{step}             process step

User (auth)
  GET  /dashboard                  user dashboard

Admin (auth + role:admin|manager)
  GET  /admin/dashboard            dashboard with stat cards + recent users
  GET  /admin/users                user list
  GET  /admin/users/create         create form
  POST /admin/users                store
  GET  /admin/users/{id}/edit      edit form
  PUT  /admin/users/{id}           update
  PATCH /admin/users/{id}/suspend  suspend
  PATCH /admin/users/{id}/activate activate
  GET  /admin/settings             settings form
  PUT  /admin/settings             update settings
  GET  /admin/activity             activity log (paginated)
```

---

## Database

| Table | Purpose |
|---|---|
| `users` | Auth + `status` (active/suspended) |
| `roles` `permissions` `model_has_roles` `model_has_permissions` `role_has_permissions` | Spatie RBAC |
| `activity_log` | Spatie activitylog |
| `settings` | Key-value store |
| `cache` `jobs` `sessions` | Laravel internals |

---

## Installation

### Shared Hosting (recommended)
1. Upload files, set document root to `/public`
2. Visit `yourdomain.com/install`
3. Follow the 5-step wizard — done

### Local Dev
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
npm install && npm run dev
php artisan serve
```

---

## Tests

```bash
php artisan test
```

**80 tests, all green.**

| Suite | Tests |
|---|---|
| Auth | login, register, suspended block |
| User CRUD | create, edit, suspend, activate |
| Settings | get/set, admin form |
| Activity | log entries, pagination |
| UI Components | 38 component render tests |
| Installer | 17 tests (guard, steps, finish) |

---

## Tech Stack

- **Laravel 13** · PHP 8.2+
- **Alpine.js v3** — interactive UI (sidebar, modals, dropdowns)
- **Spatie laravel-permission** — RBAC
- **Spatie laravel-activitylog** — audit trail
- **Vite** — asset bundling
- **Inter** — font (Google Fonts)

---

## Update Packages

Each release ships a zip archive with only changed files (directory structure preserved) for drop-in deployment on shared hosting.

See [CHANGELOG.md](CHANGELOG.md) for per-version changes.
