# app/Models

Eloquent models. Thin by design — no business logic, only relationships, casts, fillable declarations, and small static helpers that are tightly coupled to the model's own table.

## Files

| File | Purpose |
|---|---|
| `User.php` | Core auth model. Uses `HasApiTokens` (Sanctum), `HasRoles` (Spatie), `SoftDeletes`, `Notifiable`, `HasFactory`. Implements `MustVerifyEmail`. Fillable and hidden fields declared via PHP 8 attributes (`#[Fillable]`, `#[Hidden]`). `locale` column drives `SetLocale` middleware. `avatar` holds a relative path on the `public` disk. |
| `Setting.php` | Key-value store for app-wide configuration. Static helpers: `get(key, default)`, `set(key, value)`, `setMany([key => value])`, `flushCache()`. Request-scoped in-memory cache (`static $cache`) avoids repeated DB hits per request. All settings are strings in `settings` table. |
| `Language.php` | Represents a UI language (code, name, flag emoji, is_default). Has many `LanguageLine`. |
| `LanguageLine.php` | A single translation line: `language_id`, `key` (dot-notation, e.g. `auth.failed`), `value`. Belongs to `Language`. |

## Conventions

- Declare `$fillable` / `$hidden` as PHP 8 attribute annotations (`#[Fillable]`, `#[Hidden]`) on `User` — match the existing style, not the `protected $fillable = []` array syntax.
- Other models use the classic `protected $fillable` array — do not mix styles within the same model.
- Relationships are defined with return-typed methods (`HasMany`, `BelongsTo`, etc.).
- No business logic in models. If you need more than a cast or a relationship, put it in a Service.
- All boolean-like settings stored in `Setting` use the string `'1'` / `'0'` convention — never PHP `true`/`false` in the DB.
- `Setting` has a request-scoped in-memory cache (`static $cache`). Call `Setting::flushCache()` in tests after DB rollback — static properties survive across test methods. `TestCase::setUp()` does this automatically.

## Gotchas

- `User` uses `SoftDeletes` — `User::find()` excludes trashed users. Use `User::withTrashed()` or `User::onlyTrashed()` explicitly in admin restore flows.
- The `password` cast is `'hashed'` (Laravel 10+ auto-hashing cast). Do not call `Hash::make()` before assigning to `$user->password` — it will double-hash.
- `Language` and `LanguageLine` back the DB-driven translation system. The `LangKeyExtractor` service seeds keys from PHP lang files into `language_lines` — the two sources must stay in sync.
- `Setting` has no timestamps (`$timestamps` is not explicitly disabled) — verify the migration if adding created_at/updated_at is needed.
- Spatie `HasRoles` adds `roles()` and `permissions()` relations — do not define these manually on `User`.
