# Session State — Dravion SaaS Starter Kit

> Updated: 2026-06-15 (session 3) | Version: 1.4.0

## Current State

- **Tests:** 414/414 passing, 6 risky (acceptable)
- **Branch:** main, up to date with origin
- **Last commit:** (v1.4.0 release tag) — feat: public layout polish — logo/theme/lang/user-dropdown/settings

## Completed This Session

1-12. [previous sessions] security, UI, auth, notifications, API tokens, sessions
13. **#28** — Session management (SessionController + sessions.blade.php) ✓
14. **#29** — Users Export CSV ✓ (already existed)
15. **#26/#27** — Notifications bell + Sanctum tokens ✓ (already existed)
16. **Public website** — HomeController, public layout, CMS Pages admin CRUD, seed pages
17. **Hero images** — pages table hero_image/title/subtitle/cta; admin edit/create forms; home/contact/gallery heroes with Unsplash bg + dark overlay; PageHeroSeeder
18. **Public layout polish** — theme icons (CSS hidden dark:block, exact admin SVGs), lang switcher (@auth), logo from Settings, user dropdown with avatar, app_name from Settings everywhere
19. **Admin sidebar/header** — logo + app name from Settings (no hardcoded DRAVION)
20. **Settings: Public Site section** — header_tagline, footer_text, footer_copyright editable from admin
21. **v1.4.0 release** — CHANGELOG + config/dravion.php + git tag v1.4.0

## Pending / Next Steps (ordered)

- [ ] **#21** Auth — 2FA / TOTP
- [ ] Public site styling — polish hero, nav, dark mode toggle on public pages
- [ ] CMS Pages — rich text editor (TipTap/Quill) instead of raw HTML textarea

## Architecture Snapshot

```
Laravel 13 / PHP 8.3 / Tailwind v4 / Alpine.js v3 / Sanctum
Public:   GET / → HomeController@index; GET /p/{slug} → HomeController@show
          layouts/public.blade.php — responsive header + nav from DB + auth buttons
CMS:      pages table (title,slug,content,excerpt,is_published,show_in_nav,sort_order,
          hero_image,hero_title,hero_subtitle,hero_cta_label,hero_cta_url)
          App\Models\Page; Admin\PagesController resource; seeded: Home,About,Pricing,Contact
          Hero fields editable from admin pages edit/create; PageHeroSeeder sets Unsplash defaults
Public:   layouts/public.blade.php — logo from Settings, app_name from Settings,
          theme toggle (CSS hidden/dark:block, exact admin SVGs), lang switcher (@auth),
          user dropdown with avatar, footer_text + footer_copyright from Settings
Settings: app_name, logo, header_tagline, footer_text, footer_copyright, broadcast_banner
          Stored via Setting::get/setMany; SettingsController handles upload + all keys
Sidebar:  logo + app_name from Settings; no hardcoded strings
Auth:     LoginController (manual) + MustVerifyEmail + VerificationController
Sanctum:  HasApiTokens on User; GET/POST/DELETE /api-tokens
Bell:     NotificationController JSON feed; Alpine.js dropdown in app-header
Notifs:   DB channel on suspend/activate (→user) + new-user/update (→admins)
License:  LicenseService → HMAC cache → license server
Updater:  UpdaterService → GitHub API → copyTree()
Roles:    Spatie (admin/manager/editor/user)
i18n:     lang/en/ + lang/bg/ — 17 files including pages.php
Tests:    SQLite in-memory, Http::fake() for external calls
```

## Standing Instructions (always active)

- caveman + tdd — active every prompt
- Multi-agent — Explore/Plan/CSO per task
- Push after every completed change
- End every task with "Готов съм!"
- All UI strings via `__()`, never hardcoded in Blade
- Update ALL relevant CLAUDE.md files after every task
- Update STATE.md at session end
