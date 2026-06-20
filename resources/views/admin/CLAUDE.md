# resources/views/admin/ — CLAUDE.md

Admin panel views for Dravion SaaS Starter Kit.

## Access control
All admin routes are behind `['auth', 'role:admin|manager|editor', 'license.check']`.
Individual routes add granular `can:` permission middleware (e.g. `can:view users`).
Views must never assume the user has a specific permission — always guard with
`@can('edit users')` / `@cannot` directives in templates if UI elements should
be conditionally visible.

Roles with elevated access:
- `admin` — full access, including roles, updates, license
- `manager`, `editor` — subset of permissions configured via roles/permissions UI

## File inventory

### Root-level pages
| File | Route | Purpose |
|---|---|---|
| `dashboard.blade.php` | `admin.dashboard` | Stats, recent activity, cache clear |
| `activity.blade.php` | `admin.activity` | Activity log table with search |
| `settings.blade.php` | `admin.settings` | App settings form + SMTP test |
| `license.blade.php` | `admin.license` | License key management — show current key (masked), activate new key, remove key |

### users/
| File | Purpose |
|---|---|
| `index.blade.php` | Paginated user table; search, role/status filter, export CSV |
| `create.blade.php` | Create user form |
| `edit.blade.php` | Edit user; includes suspend/activate/delete actions |

### roles/
| File | Purpose |
|---|---|
| `index.blade.php` | Role list + permission matrix; admin-only (`role:admin`) |

### languages/
| File | Purpose |
|---|---|
| `index.blade.php` | Language list; set default, delete, import/export |
| `edit.blade.php` | Inline translation key editor with batch save |
| `meta.blade.php` | Language metadata (name, locale code, flag) |

### updates/
| File | Purpose |
|---|---|
| `index.blade.php` | Self-updater UI; version check + one-click install; admin-only |

### showcase/
UI component showcase pages (TailAdmin elements). These are for reference only —
not linked from the production sidebar. Routes registered under `admin.ui.*`.

Files: `alerts`, `avatars`, `badges`, `bar-chart`, `blank`, `buttons`, `calendar`,
`ecommerce`, `form-elements`, `images`, `line-chart`, `profile`, `tables`, `videos`.

## Conventions

### Layout
Every admin view must open with the admin layout component:

```blade
<x-layouts.admin :title="__('nav.some_page')">
    {{-- page content --}}
</x-layouts.admin>
```

### i18n
All human-readable text must use `__()`. Admin views use these translation namespaces:

- `app.*` — generic actions (save, cancel, delete, search…)
- `users.*` — user management strings
- `roles.*` — role/permission strings
- `settings.*` — settings page strings
- `activity.*` — activity log strings
- `updates.*` — updater strings
- `license.*` — license strings
- `nav.*` — sidebar/breadcrumb labels
- `flash.*` — toast messages returned from controllers

### Dark mode
Use solid dark: classes only:

```blade
{{-- CORRECT --}}
class="bg-white dark:bg-gray-800"
class="border-gray-200 dark:border-gray-700"
class="text-gray-800 dark:text-white/90"

{{-- WRONG --}}
dark:bg-white/[0.03]
```

### Confirm modal pattern
Destructive actions (delete, suspend, restore) use the global confirm modal
dispatched via Alpine custom events — do NOT use `<form method="POST">` with
`@method('DELETE')` inline. Use the event pattern:

```blade
<button type="button"
    @click="window.dispatchEvent(new CustomEvent('confirm-action', { detail: {
        title: '{{ __('users.delete_confirm_title') }}',
        message: '{{ __('users.delete_confirm_msg') }}',
        btnLabel: '{{ __('app.delete') }}',
        btnColor: '#ef4444',
        url: '{{ route('admin.users.destroy', $user) }}',
        method: 'DELETE',
        successAction: 'remove',
        targetId: 'row-{{ $user->id }}',
        toastMessage: '{{ __('flash.user_deleted') }}',
        toastVariant: 'success'
    }}))">
    {{ __('app.delete') }}
</button>
```

### Row IDs for DOM manipulation
Table rows that are updated/removed via the confirm modal must have
`id="row-{{ $model->id }}"` on the `<tr>` element.

### Pagination
Use Laravel's paginator with `->links()`. Pass search/filter params:

```blade
{{ $users->appends(request()->only('search','role','status'))->links() }}
```

## Gotchas
- The updates page and roles page are `role:admin` only — do not link them
  from views visible to `manager` or `editor` without `@role('admin')` guard.
- Language edit (`languages/edit.blade.php`) uses Alpine.js for inline editing
  with batch save — do not replace this with full page reloads.
- Settings SMTP test fires a POST to `admin.settings.smtp-test` and returns JSON;
  handle the response in Alpine, not with a redirect.
