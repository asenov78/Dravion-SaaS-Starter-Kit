# Changelog

All notable changes to Dravion SaaS Starter Kit.

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
