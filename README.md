# Dravion SaaS Starter Kit

**Laravel 13 · PHP 8.3 · Tailwind v4 · Alpine.js v3 · TipTap v3**

Production-ready SaaS starter kit with multi-role admin panel, CMS pages, license system, and self-updater. Designed for shared hosting deployment.

---

## Features

### Admin Panel
- Multi-role system (admin / manager / editor / user) via Spatie laravel-permission v8
- Granular permission matrix — manage per-role access from UI (Users, Pages, Settings, Activity Log)
- User management: CRUD, suspend/activate, soft-delete & restore, bulk actions, CSV export
- Activity log with filters (event, user, date range) and CSV export
- App settings: logo, SMTP, timezone, date format, maintenance mode, broadcast banner, footer
- Language management: add locales, edit translations inline, import/export JSON
- CMS Pages: TipTap v3 editor with live split-pane preview (auto-scrolls to cursor position)
- In-app notifications bell (read/unread feed)
- API tokens management (Laravel Sanctum)
- Session management (view active sessions, kill other devices)
- Self-updater: GitHub releases, license-gated, one-click install

### Public Site
- Marketing landing page with hero section, features grid, CTA
- Dynamic CMS pages (`/p/{slug}`) — content managed from admin panel
- Contact form (saved to DB + admin email notification, rate limited 5/min)
- Multi-language public UI (EN + BG included)
- Dark/light mode toggle

### Security
- Rate limiting: login (5/min), register (3/min), forgot-password (3/min)
- Session: encrypted, secure cookie, SameSite=lax, regenerated on login
- Suspended users blocked before session creation
- All admin routes: `auth` + `role:admin|manager|editor` + `license.check`
- Granular `can:` permission middleware on every sensitive route
- CSRF, mass-assignment protection (`$fillable`), input validation throughout
- Password reset: token-based (not plain password email)
- Email verification support (MustVerifyEmail)
- Avatar uploads: GD re-encode (eliminates embedded payloads), max 2MB

### License System
- Purchase code activation tied to domain
- Weekly license ping — admin warning banner on failure
- Self-updater gated behind valid license

---

## Requirements

| Requirement | Minimum |
|---|---|
| PHP | 8.2+ |
| MySQL / MariaDB | 5.7+ / 10.3+ |
| Composer | 2.x |
| Node.js (build only) | 18+ |

**Required PHP Extensions:** PDO, PDO MySQL, Mbstring, OpenSSL, Tokenizer, JSON, BCMath, Fileinfo

---

## Installation (Shared Hosting)

### Option A: Web Installer (Recommended)

1. Download `dravion-v1.5.0.zip`
2. Upload and extract to your server (e.g. `public_html/`)
3. Point your domain document root to the `public/` subfolder
4. Ensure `storage/` and `bootstrap/cache/` are writable (`chmod 755`)
5. Visit `https://yourdomain.com/install`

**Installer steps:**
| Step | What it does |
|---|---|
| Requirements | Checks 12 PHP extensions + folder write permissions |
| Site & Database | App name, URL, MySQL credentials — tests connection before proceeding |
| Admin Account | Name, email, password (min 8 chars) |
| License | Purchase code activation (domain-locked) |
| Finish | Runs migrations, seeds roles/permissions, creates admin, writes `storage/install.lock` |

### Option B: Manual (CLI)

```bash
# Upload files, then SSH in
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
# Edit .env with your database credentials
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan storage:link
```

---

## Configuration

### .env — Shared Hosting Essentials

```env
APP_NAME="My App"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_pass

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

### Mail (SMTP)

```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=yourpassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

### Self-Updater (GitHub)

```env
GITHUB_OWNER=your-github-username
GITHUB_REPO=dravion
GITHUB_TOKEN=github_pat_...
```

---

## Roles & Permissions

| Permission | admin | manager | editor | user |
|---|:---:|:---:|:---:|:---:|
| view users | ✓ | ✓ | ✓ | — |
| create users | ✓ | ✓ | — | — |
| edit users | ✓ | ✓ | — | — |
| delete users | ✓ | — | — | — |
| suspend users | ✓ | ✓ | — | — |
| view pages | ✓ | ✓ | ✓ | — |
| create pages | ✓ | ✓ | ✓ | — |
| edit pages | ✓ | ✓ | ✓ | — |
| delete pages | ✓ | — | — | — |
| view settings | ✓ | — | — | — |
| edit settings | ✓ | — | — | — |
| view activity log | ✓ | ✓ | — | — |

All permissions are configurable from **Admin → Roles**.

---

## Tech Stack

| Layer | Technology | Version |
|---|---|---|
| Backend | Laravel | 13 |
| Language | PHP | 8.3 |
| CSS Framework | Tailwind CSS | v4 |
| JS Reactivity | Alpine.js | v3 |
| Rich Text Editor | TipTap | v3 |
| Roles & Permissions | Spatie laravel-permission | v8 |
| Activity Log | Spatie laravel-activitylog | v4 |
| API Auth | Laravel Sanctum | — |
| Build Tool | Vite | 8 |
| Tests | PHPUnit | 12 |

---

## Testing

```bash
composer test
```

414+ tests (Feature + Unit) covering: auth flows, role/permission checks, user CRUD, settings, pages, installer, notifications, sessions, API tokens.

---

## Upgrading

Admin panel → **Updates** (admin-only). Requires valid license and GitHub token configured.

---

## Version

**1.5.0** — see [CHANGELOG.md](CHANGELOG.md) for full history.

## License

Commercial. One license per domain. Contact support for transfers.
