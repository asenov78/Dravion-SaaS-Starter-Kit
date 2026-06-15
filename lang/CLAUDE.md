# lang/ — CLAUDE.md

Translation files for Dravion SaaS Starter Kit.
Locales: `en/` (English, default) and `bg/` (Bulgarian).

## File inventory

Both `en/` and `bg/` contain identical file sets:

| File | Namespace | Used for |
|---|---|---|
| `app.php` | `app.*` | Generic UI actions and labels (save, cancel, delete, search, status values…) |
| `auth.php` | `auth.*` | Auth forms: login, register, forgot-password, reset-password |
| `nav.php` | `nav.*` | Sidebar navigation labels, page titles |
| `dashboard.php` | `dashboard.*` | User/admin dashboard strings |
| `users.php` | `users.*` | User management (CRUD, filters, table headers) |
| `roles.php` | `roles.*` | Role and permission management |
| `settings.php` | `settings.*` | App settings page |
| `activity.php` | `activity.*` | Activity log page |
| `flash.php` | `flash.*` | Toast/redirect flash messages returned by controllers |
| `languages.php` | `languages.*` | Language management UI |
| `license.php` | `license.*` | License key management |
| `updates.php` | `updates.*` | Self-updater UI |
| `mail.php` | `mail.*` | Transactional email subjects and body copy |
| `permissions.php` | `permissions.*` | Human-readable permission labels |
| `tokens.php` | `tokens.*` | API tokens page (title, create, revoke, copy, etc.) |
| `notifications.php` | `notifications.*` | In-app notification text (title + body per event type) |

## Usage in Blade

Always use the `__()` helper. Never hardcode UI strings.

```blade
{{-- Correct --}}
{{ __('app.save') }}
{{ __('users.title') }}
{{ __('flash.user_created') }}

{{-- With interpolation --}}
{{ __('users.count', ['count' => $users->total()]) }}

{{-- In Alpine/JS context — pass via @json or data attribute --}}
<button @click="$store.flash.fire('{{ __('flash.saved') }}', 'success')">
```

## Adding a new translation key

1. Add the key to `lang/en/[file].php`.
2. Add the Bulgarian equivalent to `lang/bg/[file].php` simultaneously.
3. Run `php artisan lang:seed` if a seeder is wired up, or the Languages admin
   UI will show the new key as untranslated until manually edited.

### Choosing the right file
- Generic one-word actions (Save, Cancel, Delete) → `app.php`
- Page-specific strings → the page's own file (`users.php`, `settings.php`, etc.)
- Controller success/error messages → `flash.php`
- Email content → `mail.php`
- New domain area with 5+ strings → create a new file in both `en/` and `bg/`

## Key conventions

### `app.php` — reserved keys
These keys are used globally across many views. Do not change their meaning:

```php
'save'       'cancel'     'delete'     'edit'       'create'
'update'     'search'     'actions'    'status'     'active'
'suspended'  'name'       'email'      'role'       'yes'   'no'
'success'    'error'      'warning'    'confirm'    'back'  'next'
'loading'    'no_results' 'clear_cache'
```

### `flash.php` — flash message convention
Controllers call `redirect()->with('success', __('flash.some_key'))`.
The admin layout bridges this to `$store.flash.fire()`.
Keys should describe what happened, not a generic "success":

```php
// Good
'user_created'   => 'User created successfully.',
'user_deleted'   => 'User deleted.',
'settings_saved' => 'Settings saved.',

// Bad — too generic
'saved' => 'Saved.'
```

### Interpolation placeholders
Use `:param` syntax:

```php
// lang/en/users.php
'count' => ':count users found',

// In Blade
{{ __('users.count', ['count' => $total]) }}
```

## Language management via Admin UI
The admin panel (`/admin/languages`) allows:
- Adding new locales
- Editing any translation key inline (with batch save)
- Importing/exporting `.php` translation files
- Setting a locale as the application default

When a new locale is added via the UI, its PHP files are generated from the
English source. Always keep `lang/en/` as the canonical source of truth.

## Gotchas
- The `Languages` admin section edits the actual PHP files on disk. After editing
  translations via the UI, the changes are live immediately (no cache clear needed
  unless `config:cache` was run).
- Do not use `trans_choice()` unless the file has the `|` plural form defined in
  both locales.
- Bulgarian (`bg`) strings may be longer than English — ensure UI elements allow
  text overflow gracefully (use `truncate` or `min-w-0` where needed).
- Mail templates in `lang/en/mail.php` and `lang/bg/mail.php` are used inside
  Mailable classes — they must be kept in sync with the Mailable's `content()`
  method parameter names.
