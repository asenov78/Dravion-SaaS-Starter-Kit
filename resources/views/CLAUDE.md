# resources/views/ — CLAUDE.md

Dravion SaaS Starter Kit · Laravel 13 · PHP 8.3 · Blade · Alpine.js v3 · Tailwind v4

## Directory structure

```
views/
  dashboard.blade.php      — Authenticated user dashboard (auth middleware)
  welcome.blade.php        — Public landing / marketing page
  admin/                   — Admin panel pages (see admin/CLAUDE.md)
  components/              — Reusable Blade components (see components/CLAUDE.md)
  auth/                    — Login, register, forgot-password, reset-password views
  emails/                  — Mailable Blade templates
  install/                 — Installer wizard steps
  layouts/                 — Partial includes: sidebar, app-header, backdrop, etc.
```

## Core conventions

### i18n — ALL UI strings via `__()`
Never write hardcoded human-readable strings in Blade. Every label, heading,
placeholder, button text, and error message must go through the translation helper.

```blade
{{-- CORRECT --}}
{{ __('users.title') }}
{{ __('app.save') }}
{{ __('auth.login') }}

{{-- WRONG — never do this --}}
Users
Save
Sign In
```

Translation files live in `lang/en/` and `lang/bg/`. Add the key to both locales
simultaneously. See `lang/CLAUDE.md` for file layout.

### Dark mode — solid backgrounds only
Use solid Tailwind dark: classes. Never use transparent/alpha backgrounds for
dark mode surface colors.

```blade
{{-- CORRECT --}}
dark:bg-gray-800
dark:bg-gray-900
dark:bg-gray-700

{{-- WRONG --}}
dark:bg-white/[0.03]
dark:bg-black/20
```

### Alpine.js v3 interactivity
All client-side interactivity uses Alpine.js v3. Do not add Vue, React, or vanilla
event-listener spaghetti. Global Alpine stores are initialized in
`components/layouts/admin.blade.php`:

- `$store.theme`   — light/dark toggle, persisted in localStorage
- `$store.flash`   — toast/alert system; call `$store.flash.fire(msg, variant)`
- `$store.sidebar` — sidebar expand/collapse state

Dispatch a confirm modal via `window.dispatchEvent`:

```js
window.dispatchEvent(new CustomEvent('confirm-action', { detail: {
    title: '...', message: '...', btnLabel: '...', btnColor: '#ef4444',
    url: '/admin/users/1', method: 'DELETE',
    successAction: 'remove', targetId: 'row-1',
    toastMessage: '...', toastVariant: 'success'
}}));
```

### Flash messages
Controllers return `redirect()->with('success', __('flash.key'))`.
The admin layout bridges session flash to `$store.flash` automatically.
Do not manually echo session flash in individual views — it is handled globally.

### Layout usage
- Admin pages: `<x-layouts.admin :title="__('nav.page_name')">`
- Portal/user pages: `<x-layouts.portal>`
- Installer: `<x-install.layout>`

## Gotchas
- `welcome.blade.php` and auth views do NOT use `x-layouts.admin`.
- The `{{ $slot }}` is rendered inside a `max-w-(--breakpoint-2xl)` container in the admin layout; do not add another outer `max-w-*` wrapper inside admin views.
- Use `x-cloak` on any Alpine element that should be hidden until Alpine is ready.
