# Session State — Dravion SaaS Starter Kit

> Updated: 2026-06-16 (session 4) | Version: 1.6.0

## Current State

- **Tests:** 414/414 passing
- **Branch:** main, up to date with origin
- **Last commit:** c5b176f — fix: preview pane display:flex via CSS class

## Completed This Session

1. **TipTap fonts** — editor + preview unified (Onest 15px/1.75), moved cms-content CSS to app.css
2. **TipTap live split-pane** — editor left, real-time preview right (x-html="content")
3. **TipTap auto-scroll** — onSelectionUpdate → panel.scrollTop (not scrollIntoView; page stays still)
4. **Pages permissions** — can: middleware per route action; pages group in roles matrix
5. **Installer fixes** — app_name field, try/catch on migrate/seed, license label required
6. **v1.5.0 release** — CHANGELOG + config + docs + ZIP archive dravion-v1.5.0.zip (35MB)
7. **TipTap editor scroll** — fixed height 520px, overflow:hidden on container, overflow-y:auto on panes
8. **Preview scroll fix** — .tiptap-preview-pane CSS class (display:flex) — x-show no longer overrides flex
9. **Resize bar** — sibling div below tiptap editor; drag to resize height; handles inside panes were clipped
10. **HTML source font** — 12px → 14px
11. **Architecture review** — HTML report generated with 6 candidates + roadmap
12. **v1.6.0 bump** — config/dravion.php, CLAUDE.md, CHANGELOG.md

## Pending / Next Steps (priority order)

- [ ] **#21** Auth — 2FA / TOTP (URGENT)
- [ ] **#26** Notifications — In-app bell (URGENT) — NotificationController exists, needs UI
- [ ] **Arch #1** LicenseService — consolidate activate() from InstallController
- [ ] **Arch #2** Model Observers — UserObserver/PageObserver replace manual ActivityLogger calls
- [ ] **#28** Security — Session management (SessionController exists, needs UI)
- [ ] **Arch #3** EnvWriter service — atomic .env writes with file locking
- [ ] **#27** API — Sanctum tokens page (ApiTokenController exists, needs UI)
- [ ] **#29** Users — Export CSV
- [ ] **Tests** — ContactController + HomeController (untested)
- [ ] **LOW** Service interfaces: LicenseServiceInterface + DI binding
- [ ] **LOW** config/updater.php: add config/dravion.php to protected_paths
- [ ] **#20** Auth — Email verification (in progress)

## Architecture Snapshot

```
Laravel 13 / PHP 8.3 / Tailwind v4 / Alpine.js v3 / TipTap v3 / Spatie v8 / PHPUnit 12
Public:   GET / → HomeController; GET /p/{slug} → HomeController@show
CMS:      Page + PageTranslation (multilingual); TipTap editor with live preview
          Independent scroll on editor + preview; resize bar at bottom
          .tiptap-preview-pane class = display:flex (survives Alpine x-show)
Auth:     LoginController (manual suspend check) + MustVerifyEmail
Admin:    UserController, PagesController, RoleController, SettingsController
          LicenseController, UpdateController, ActivityController, GlobalSearchController
Services: LicenseService (HMAC cache), UpdaterService (GitHub ZIP), AvatarService (GD)
          ActivityLogger (Spatie wrapper) — manual in controllers (no observers yet)
Middleware: InstallGuard, LicenseCheck, SetLocale, MaintenanceMode
Roles:    Spatie: admin/manager/editor/user + fine-grained can: gates per route
i18n:     DB-driven + lang/en/ + lang/bg/ (17 files)
Install:  5-step wizard → write .env → migrate → seed → admin → install.lock
Tests:    SQLite in-memory, 414 passing, 41 test files
```

## Standing Instructions (always active)

- caveman + tdd — active every prompt
- Multi-agent — Explore/Plan/CSO per task
- Push after every completed change
- End every task with "Готов съм!"
- All UI strings via `__()`, never hardcoded in Blade
- Update ALL relevant CLAUDE.md files after every task
- Update STATE.md at session end
