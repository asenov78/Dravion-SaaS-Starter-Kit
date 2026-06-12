# TODO — Dravion SaaS Starter Kit

## Done ✓

### Core
- [x] Laravel 13 scaffold
- [x] Auth — login, register, logout, suspended block
- [x] RBAC — Spatie Permission (admin, manager, editor, user)
- [x] User Management — list, create, edit, suspend, activate
- [x] Settings — key-value DB store, admin UI
- [x] Activity Log — Spatie activitylog, User model logging
- [x] User dashboard stub (`/dashboard`)

### Admin UI
- [x] Admin layout — Quantix/Linear-style dark sidebar
- [x] Sidebar — glass backdrop, section labels (GENERAL/TOOLS/SUPPORT), 12 nav items
- [x] Sidebar — animated collapse, section labels fade, version footer
- [x] Sidebar — promo card (Installer Wizard / Coming soon)
- [x] Topbar — search, Live data pill, System healthy pill, bell, settings, user card
- [x] Page header — title + `$actions` slot (filter, export on dashboard)
- [x] Floating layout — 8px gap, border-radius:12px app shell
- [x] Animated canvas background — 55 particles, cyan network lines
- [x] Background on login + register pages (via `<x-ui.net-bg>`)

### UI Components (38 total)
- [x] button, badge, card, input, alert, label, stat
- [x] textarea, checkbox, select, radio-group, input-otp
- [x] separator, aspect-ratio, scroll-area
- [x] avatar, skeleton, spinner, progress, breadcrumb, kbd, table
- [x] dialog, sheet, drawer, alert-dialog, tooltip, hover-card, popover
- [x] tabs, accordion, dropdown, menubar, navigation-menu, context-menu, pagination
- [x] switch, toggle, toggle-group, slider, collapsible
- [x] toast, net-bg

### Installer
- [x] 5-step wizard (requirements → database → admin → license → finish)
- [x] InstallGuard middleware (blocks after install.lock)
- [x] PDO connection test
- [x] Write .env on finish
- [x] Hot-swap DB config + Artisan::call('migrate')
- [x] Seed roles on finish
- [x] Create admin user + assign role
- [x] Write storage/install.lock
- [x] 17 installer tests, all green

### Documentation
- [x] README.md — full feature docs, routes, DB tables, install guide
- [x] CHANGELOG.md — per-version history
- [x] TODO.md (this file)

---

## In Progress 🔄

- [ ] Update packages — zip archive per version (files + dirs, relative paths)

---

## Pending ⏳

### High Priority
- [ ] **Update packages** — create zip for each version tag with only changed files
- [ ] **GitHub releases** — attach zip to each GitHub release tag
- [ ] **License check middleware** — weekly ping to dravion-server, cache result
- [ ] **User dashboard** — replace stub with real page (profile, activity, etc.)
- [ ] **Welcome page** — replace default Laravel welcome with Dravion landing

### Admin Panel
- [ ] Analytics page (GENERAL > Analytics — currently placeholder)
- [ ] Billing page (GENERAL > Billing — currently placeholder)
- [ ] Messages (GENERAL > Messages — currently placeholder)
- [ ] Roles management page (TOOLS > Roles — currently placeholder)
- [ ] Automation page (TOOLS > Automation — currently placeholder)
- [ ] Security page (SUPPORT > Security — currently placeholder)
- [ ] Help / Documentation page (SUPPORT > Help — currently placeholder)

### Installer
- [ ] License activation API call on finish (ping dravion-server, non-blocking)
- [ ] Domain binding (store licensed domain in .env)
- [ ] Installer skinning — allow logo override via config

### Documentation (Codester/Envato)
- [ ] `/documentation` HTML page (required by Codester)
- [ ] Codester screenshots — 1200×800px (min 6 screenshots)
- [ ] Demo site deployment on shared hosting

### Deployment
- [ ] Deploy demo to shared hosting
- [ ] Set up CI/CD (GitHub Actions — tests on push)
- [ ] `.env.example` — update with all new keys (DRAVION_LICENSE_KEY, etc.)

---

## Release Workflow

For every version bump:
1. Update `config/dravion.php` — `version`
2. Update `CHANGELOG.md` — add entry
3. `git commit` + `git push`
4. Create zip update package (changed files only, with directory structure)
5. Create GitHub release tag, attach zip

### Update Package Format
```
dravion-update-v1.X.X.zip
└── (files with full relative paths from project root)
    e.g. resources/views/components/layouts/admin.blade.php
         config/dravion.php
```

Use PowerShell `Compress-Archive` with relative paths (NOT -j / absolute paths).
