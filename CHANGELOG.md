# Changelog

All notable changes to Dravion SaaS Starter Kit.

## [1.4.0] — 2026-06-15
### Added
- Public website: full marketing landing page at `/` with hero, features grid, security section, stack section, CMS pages
- Contact page (`/contact`): form saved to DB + optional email, info cards
- Gallery page (`/gallery`): component showcase with CSS mockups
- CMS pages: admin CRUD for public pages with hero image, title, subtitle, CTA fields (editable per page from admin)
- Hero background images (Unsplash) on home, contact, gallery — dark overlay + grid pattern
- Public layout: matches admin header design — same theme toggle (sun/moon SVGs), language switcher, logo from Settings, user dropdown with avatar
- Admin sidebar: logo from Settings, app name from Settings (no more hardcoded "DRAVION")
- Admin header: logo + app name dynamic on mobile header
- Settings: new "Public Site" section — header tagline, footer text, footer copyright editable from admin
- Footer: uses app_name from Settings, shows footer_text + footer_copyright settings
- Sessions and API tokens pages: smart layout (admin vs portal) + dark mode fixes
- `View Site` globe button in admin header → opens public site in new tab

### Changed
- Root `/` serves public home page (not redirect to dashboard); install check preserved
- All public pages use `app_name` from Settings (not hardcoded config)
- Pages migration: added `hero_image`, `hero_title`, `hero_subtitle`, `hero_cta_label`, `hero_cta_url` fields

## [1.3.1] — 2026-06-15
### Added
- Updates page: per-version changelog — every release newer than the current version is listed with its notes, newest first, with a "latest" badge
### Changed
- Updater integrates with license: latest version is always visible (even unlicensed), but download/install stays license-gated
- `UpdaterService` now reads the full GitHub releases list instead of only the latest release

## [1.3.0] — 2026-06-15
### Added
- Self-updater: admin-only `/admin/updates` — checks GitHub releases, license-gated, one-click install (maintenance mode, file copy, migrate, cache clear)
- `LicenseService` + `UpdaterService`; `config/updater.php`; release workflow on `v*.*.*` tags
- Avatar upload for users & profile (GD resize to 200px), shown in dashboard/user lists
- Settings: logo upload, SMTP test button, welcome-email toggle
- Dashboard: system health widget (PHP, Laravel, disk, DB size, cache driver)
- Notifications: welcome mail on user create, account suspended/activated mails
### Changed
- Users: soft-delete restore, role/status filters, CSV export, trash tab
- Roles & Permissions: grouped permission matrix, per-permission route guards, confirm modal
- Global session flash → Alpine store bridge for all controllers
- Full EN/BG i18n coverage for new UI

## [1.1.8] — 2026-06-11
### Changed
- Admin sidebar: Quantix-style redesign — glass bg, section labels (GENERAL/TOOLS/SUPPORT), collapse in header, promo card, version footer, gradient avatar

## [1.1.6] — 2026-06-11
### Changed
- Admin layout: replaced static bg.jpg with CSS-animated canvas network (dark blue + cyan nodes/lines, 55 particles, `requestAnimationFrame`)

## [1.1.5] — 2026-06-11
### Changed
- Admin layout: full-page dark blue geometric background image (`public/images/bg.jpg`), sidebar and topbar transparent

## [1.1.2] — 2026-06-11
### Fixed
- Sidebar: Alpine `:style` string was replacing entire style attr (losing display:flex) — switched to object syntax `{ width: ... }`
- Sidebar: user avatar + collapse button centered when collapsed, visible chevron `›`

## [1.1.1] — 2026-06-11
### Fixed
- Sidebar: collapse button arrow smaller (14px single chevron, not double arrow)
- Sidebar: html/body height:100%+overflow:hidden so nav fills full height and user+collapse stays pinned at bottom

## [1.1.0] — 2026-06-11
### Fixed
- Admin layout: complete redesign — Linear/DataNest style sidebar, proper active states, user info at bottom, collapse button
- Settings page: settings table migration was missing (now runs on deploy)
- Alert Dialog: hardcoded DELETE method — now accepts `method` prop (suspend uses PATCH)
- `$errors` null guard in create/edit views (safe outside web middleware)
- Dashboard: removed Tailwind grid classes, pure inline styles for consistency
- All views: unified inline-style approach, no mixed Tailwind/inline

## [1.0.0] — 2026-06-11
### Added
- All views refactored to use `<x-ui.*>` components (login, register, dashboard, users/index, users/create, users/edit)
- Settings page: key-value DB store, `Setting` model with `get/set/setMany` helpers
- Activity Log page: spatie/activitylog integration, paginated table with causer avatars and tooltips
- `SettingsController`, `ActivityController`
- User model logs activity on name/email/status changes via `LogsActivity` trait
- Button component: added `tag` + `href` props for link rendering
- 80 tests green

## [0.9.0] — 2026-06-11
### Added
- Batch E components: Menubar, Navigation Menu, Context Menu
- shadcn/ui component parity COMPLETE — 38 components total
- 4 new unit tests — 80 total, all green

## [0.8.0] — 2026-06-11
### Added
- Batch D components: Alert Dialog, Slider, Aspect Ratio, Popover, Toggle Group, Input OTP, Scroll Area
- 8 new unit tests — 76 total, all green

## [0.7.0] — 2026-06-11
### Added
- Batch C components: Pagination, Toast, Drawer, Hover Card, Collapsible
- 7 new unit tests — 68 total, all green

## [0.6.0] — 2026-06-11
### Added
- Batch B Alpine.js components: Accordion, Tabs, Dialog, Dropdown, Tooltip, Switch, Toggle, Sheet
- 9 new unit tests — 61 total, all green

## [0.5.0] — 2026-06-11
### Added
- Batch A UI components: Separator, Avatar, Skeleton, Spinner, Progress, Breadcrumb
- Batch A form components: Textarea, Checkbox, Select, Radio Group, Table, Kbd
- 18 new unit tests — 52 total, all green

## [0.4.0] — 2026-06-11
### Added
- Blade UI component library (`<x-ui.*>`): button, badge, card, input, alert, label, stat
- 12 unit tests for UI components — all green (34 total)

## [0.3.0] — 2026-06-11
### Added
- Admin layout: Linear-style dark sidebar, collapsible with Alpine.js + localStorage
- Dashboard view: stat cards, recent users table
- Users index view: avatar, role/status badges, suspend/activate actions

## [0.2.0] — 2026-06-11
### Added
- User Management CRUD (list, create, edit, suspend) — Slice 3
- Admin routes with role-based access (admin, manager)

## [0.1.0] — 2026-06-11
### Added
- Laravel 13 scaffold
- Authentication: login, register, logout, suspended user block
- Role-Based Access Control: admin, manager, editor, user (Spatie Permission)
- 12 feature tests — all green

## [1.2.3] — 2026-06-12
### Added
- README.md — complete rewrite: features, routes table, DB tables, install guide, tech stack
- TODO.md — full done/pending/workflow checklist
### Changed
- CHANGELOG.md — added missing 1.1.3–1.1.9 entries
