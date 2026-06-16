# TODO — Dravion SaaS Starter Kit

## Done ✓

### Core
- [x] Laravel 13 scaffold
- [x] Auth — login, register, logout, suspended block
- [x] RBAC — Spatie Permission (admin, manager, editor, user)
- [x] User Management — list, create, edit, suspend, activate, bulk actions, soft-delete restore
- [x] Settings — key-value DB store, admin UI, logo upload, SMTP test, welcome-email toggle
- [x] Activity Log — Spatie activitylog, filter UI, export CSV
- [x] User dashboard stub (`/dashboard`)
- [x] Rate limiting — login (5/min), register (3/min), forgot-password, contact (5/min)
- [x] Session security — regenerate on login, CSRF regenerate on logout, SESSION_ENCRYPT, SESSION_SECURE_COOKIE
- [x] Suspended check BEFORE session creation
- [x] No plain password in welcome email
- [x] Avatar upload — GD resize 200×200, MIME validation, max 2MB

### Admin UI
- [x] Admin layout — Quantix/Linear-style dark sidebar
- [x] Sidebar — glass backdrop, section labels, collapse, version footer
- [x] Topbar — global search (3-char autosearch), bell, theme toggle, user card
- [x] Animated canvas background — 55 particles, cyan network lines
- [x] Dashboard — system health widget (PHP, Laravel, disk, DB, cache)
- [x] Alert — site-wide broadcast banner (admin dismissible)

### UI Components (38 total)
- [x] button, badge, card, input, alert, label, stat
- [x] textarea, checkbox, select, radio-group, input-otp
- [x] separator, aspect-ratio, scroll-area
- [x] avatar, skeleton, spinner, progress, breadcrumb, kbd, table
- [x] dialog, sheet, drawer, alert-dialog, tooltip, hover-card, popover
- [x] tabs, accordion, dropdown, menubar, navigation-menu, context-menu, pagination
- [x] switch, toggle, toggle-group, slider, collapsible
- [x] toast, net-bg

### Roles & Permissions
- [x] Roles management — create, rename, delete
- [x] Permission matrix — grouped, per-route guards
- [x] Pages permissions — can: middleware per action

### Public Website
- [x] Landing page (`/`) — hero, features, security section, stack section
- [x] Contact page (`/contact`) — form → DB + optional email
- [x] Gallery page (`/gallery`) — component showcase
- [x] CMS pages — admin CRUD, TipTap editor with live split-pane preview
- [x] TipTap — HTML source view, auto-scroll, independent scroll, resize bar
- [x] Public layout — theme toggle, language switcher, logo from Settings

### Notifications
- [x] Welcome email on user create (toggle in Settings)
- [x] Account suspended / activated emails

### License & Updates
- [x] LicenseService — HMAC cache, weekly ping
- [x] UpdaterService — GitHub releases, ZIP download, file copy, migrate
- [x] Self-updater admin UI — per-version changelog, one-click install
- [x] License middleware — gates updater features

### i18n
- [x] DB-driven translations + lang/en/ + lang/bg/ (17 files)
- [x] Language management admin UI
- [x] SetLocale middleware

### Installer
- [x] 5-step wizard (requirements → database → admin → license → finish)
- [x] InstallGuard middleware (blocks after install.lock)
- [x] PDO connection test, write .env, migrate, seed, write install.lock
- [x] app_name field, try/catch on migrate/seed
- [x] 17 installer tests, all green

### Documentation
- [x] README.md — full feature docs, routes, DB tables, install guide
- [x] CHANGELOG.md — per-version history
- [x] Release ZIP (dravion-v1.5.0.zip + v1.6.0 in progress)

---

## In Progress 🔄

- [ ] **#20** Auth — Email verification (MustVerifyEmail + middleware)

---

## Pending ⏳

### URGENT
- [ ] **#21** Auth — 2FA / TOTP
- [ ] **#26** Notifications — In-app bell + read/unread feed (NotificationController exists, needs UI)

### HIGH
- [ ] **#28** Security — Session management / kill active sessions (SessionController exists, needs UI)
- [ ] **#36** Security — Log failed login attempts (`LoginController:29` — no ActivityLogger on auth failure, A09)
- [ ] **#37** Security — .env write race condition: `LicenseController::writeEnvKey()` + `InstallController::writeEnv()` — no flock(); `addslashes()` misses `$`, `#` in passwords

### MEDIUM
- [ ] **#27** API — Sanctum tokens page (ApiTokenController exists, needs UI)
- [ ] **#29** Users — Export CSV
- [ ] **#32** Arch — EnvWriter service (atomic .env writes with flock(); blocks #37)
- [ ] **#30** Arch — LicenseService: consolidate activate() from InstallController
- [ ] **#31** Arch — Model Observers: UserObserver/PageObserver replace manual ActivityLogger calls

### LOW
- [ ] **#33** Tests — ContactController + HomeController (untested)
- [ ] **#34** Arch — LicenseServiceInterface + DI binding
- [ ] **#35** Fix — config/updater.php: add config/dravion.php to protected_paths

### Deployment / Release
- [ ] Deploy demo to shared hosting
- [ ] Set up CI/CD (GitHub Actions — tests on push)
- [ ] `/documentation` HTML page (required by Codester)
- [ ] Codester screenshots — 1200×800px (min 6 screenshots)

---

## Release Workflow

For every version bump:
1. Update `config/dravion.php` — `version`
2. Update `CHANGELOG.md` — add entry
3. `git commit` + `git push`
4. Create GitHub release tag, attach zip

### Update Package Format
```
dravion-update-v1.X.X.zip
└── (files with full relative paths from project root)
```

Use PowerShell `Compress-Archive` with relative paths (NOT -j / absolute paths).
