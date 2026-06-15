# resources/views/components/ — CLAUDE.md

Blade component library for Dravion SaaS Starter Kit.

All components are used via `<x-[group].[name]>` syntax.

## Component groups

### layouts/
Full-page shell layouts. Always used as the outermost wrapper of a view.

| Component | Tag | Purpose |
|---|---|---|
| `admin.blade.php` | `<x-layouts.admin>` | Admin panel shell: sidebar, header, flash bridge, confirm modal, Alpine stores |
| `portal.blade.php` | `<x-layouts.portal>` | Authenticated user portal shell |

`admin.blade.php` initializes three global Alpine stores on `alpine:init`:
- `$store.theme` — dark/light mode, persisted in localStorage
- `$store.flash` — toast system: `$store.flash.fire(message, variant, duration)`
- `$store.sidebar` — sidebar expand/collapse/hover state

### ui/
Generic UI primitives. These are the building blocks — prefer these over raw HTML.

| Component | Tag | Notes |
|---|---|---|
| `button.blade.php` | `<x-ui.button>` | Props: `variant` (primary/secondary/danger/ghost), `size` (sm/md/lg), `tag`, `href`. Uses inline styles — dark mode is inherent. |
| `badge.blade.php` | `<x-ui.badge>` | Colored status badges |
| `alert.blade.php` | `<x-ui.alert>` | Inline alert box |
| `alert-dialog.blade.php` | `<x-ui.alert-dialog>` | Modal alert dialog |
| `modal.blade.php` | `<x-ui.modal>` | Generic modal wrapper |
| `dialog.blade.php` | `<x-ui.dialog>` | Dialog with header/body/footer slots |
| `drawer.blade.php` | `<x-ui.drawer>` | Slide-in panel |
| `card.blade.php` | `<x-ui.card>` | Card container |
| `input.blade.php` | `<x-ui.input>` | Styled text input |
| `textarea.blade.php` | `<x-ui.textarea>` | Styled textarea |
| `select.blade.php` | `<x-ui.select>` | Styled select |
| `checkbox.blade.php` | `<x-ui.checkbox>` | Checkbox with label |
| `switch.blade.php` | `<x-ui.switch>` | Toggle switch |
| `label.blade.php` | `<x-ui.label>` | Form label |
| `table.blade.php` | `<x-ui.table>` | Table wrapper |
| `pagination.blade.php` | `<x-ui.pagination>` | Laravel paginator wrapper |
| `tabs.blade.php` | `<x-ui.tabs>` | Tab navigation |
| `accordion.blade.php` | `<x-ui.accordion>` | Collapsible sections |
| `dropdown.blade.php` | `<x-ui.dropdown>` | Dropdown menu |
| `tooltip.blade.php` | `<x-ui.tooltip>` | Hover tooltip |
| `avatar.blade.php` | `<x-ui.avatar>` | User avatar |
| `spinner.blade.php` | `<x-ui.spinner>` | Loading spinner |
| `skeleton.blade.php` | `<x-ui.skeleton>` | Loading skeleton |
| `stat.blade.php` | `<x-ui.stat>` | Stat/metric card |
| `toast.blade.php` | `<x-ui.toast>` | Toast notification |
| `breadcrumb.blade.php` | `<x-ui.breadcrumb>` | Breadcrumb navigation |
| `progress.blade.php` | `<x-ui.progress>` | Progress bar |
| `separator.blade.php` | `<x-ui.separator>` | Horizontal divider |
| `net-bg.blade.php` | `<x-ui.net-bg>` | Decorative grid/net background |
| `kbd.blade.php` | `<x-ui.kbd>` | Keyboard shortcut display |

### common/
Shared partials used inside admin views.

| Component | Tag | Purpose |
|---|---|---|
| `page-breadcrumb.blade.php` | `<x-common.page-breadcrumb>` | Breadcrumb with page title |
| `component-card.blade.php` | `<x-common.component-card>` | Showcase card wrapper |
| `dropdown-menu.blade.php` | `<x-common.dropdown-menu>` | General dropdown trigger+panel |
| `preloader.blade.php` | `<x-common.preloader>` | Full-page loading overlay (on first paint) |
| `table-dropdown.blade.php` | `<x-common.table-dropdown>` | Actions dropdown for table rows |

### form/
Form control wrappers and grouped patterns.

| Component | Tag | Purpose |
|---|---|---|
| `date-picker.blade.php` | `<x-form.date-picker>` | Date picker input |
| `input/radio.blade.php` | `<x-form.input.radio>` | Styled radio button |
| `select/multiple-select.blade.php` | `<x-form.select.multiple-select>` | Multi-select dropdown |
| `form-elements/*` | Various | Showcase variants of form inputs |

### header/
| Component | Tag | Purpose |
|---|---|---|
| `user-dropdown.blade.php` | `<x-header.user-dropdown>` | Top-right user menu: avatar, profile link, logout |

### install/
| Component | Tag | Purpose |
|---|---|---|
| `layout.blade.php` | `<x-install.layout>` | Installer wizard shell layout |

### profile/
Profile page card components.

| Component | Tag |
|---|---|
| `profile-card.blade.php` | `<x-profile.profile-card>` |
| `personal-info-card.blade.php` | `<x-profile.personal-info-card>` |
| `address-card.blade.php` | `<x-profile.address-card>` |

### ta/ (TailAdmin primitives)
Low-level TailAdmin-flavored components. Use `ui/` equivalents where available.

| Component | Tag |
|---|---|
| `avatar.blade.php` | `<x-ta.avatar>` |
| `badge.blade.php` | `<x-ta.badge>` |
| `button.blade.php` | `<x-ta.button>` |
| `youtube-embed.blade.php` | `<x-ta.youtube-embed>` |

### tables/
Showcase table variants only. Not used in production views.

### ecommerce/
Dashboard chart/metric components (demo data). Used in `admin/showcase/ecommerce.blade.php`.

### calender-area.blade.php
Standalone calendar widget component.

## Conventions

### i18n in components
Components that render user-facing text must also use `__()`. Never hardcode strings.

### Dark mode
Use solid Tailwind dark: classes. Never use transparent alpha variants for backgrounds:

```blade
{{-- CORRECT --}}
dark:bg-gray-800
dark:border-gray-700

{{-- WRONG --}}
dark:bg-white/[0.03]
dark:bg-gray-900/50
```

### Alpine.js v3
- Use `x-data`, `x-bind`, `x-on`, `x-show`, `x-transition`, `x-text`, `x-model`
- Access global stores with `$store.theme`, `$store.flash`, `$store.sidebar`
- Use `x-cloak` on elements that should be hidden before Alpine initialises
- Do NOT use `x-data` in `<x-layouts.admin>` child components to re-declare
  stores — access them via `$store` from any nested element

### `<x-ui.button>` usage
```blade
<x-ui.button variant="primary" href="{{ route('admin.users.create') }}">
    {{ __('users.add') }}
</x-ui.button>

<x-ui.button variant="danger" type="button" @click="...">
    {{ __('app.delete') }}
</x-ui.button>
```
Props: `variant` (primary/secondary/danger/ghost), `size` (sm/md/lg),
`type` (button/submit), `href` (renders `<a>` instead of `<button>`).

## Gotchas
- `<x-common.preloader>` is included once in `layouts/admin.blade.php` — do not
  add it again inside individual views.
- `<x-header.user-dropdown>` reads the authenticated user from `auth()->user()` —
  ensure it is only used inside `auth` middleware routes.
- The `ta/` components duplicate some `ui/` functionality; prefer `ui/` for new work.
