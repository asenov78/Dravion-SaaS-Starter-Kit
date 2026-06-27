# Changelog

All notable changes to Dravion SaaS Starter Kit.

## [1.15.21] — 2026-06-27
requires: 1.15.20

### Added
- Custom Data actions (create/update/delete category и field) логват в Activity Log (`log_name: custom_data`)
- `activity_log_custom_data` setting в DefaultSettingsSeeder (default: enabled)
- `CustomCategoryFactory` и `CustomFieldFactory` за тестове
- `HasFactory` trait на `CustomCategory` и `CustomField` модели
- 6 нови теста за activity logging в CustomDataTest

## [1.15.20] — 2026-06-25
requires: 1.15.19

### Fixed
- Custom Data: премахнат дублиран success alert — `session('success')` в blade + layout toast бяха едновременно активни

## [1.15.19] — 2026-06-25
requires: 1.15.18

### Changed
- Custom Data модали (Add/Edit Category, Add/Edit Field) — плосък `p-6` layout без header separator и X бутон, съвпадащ с confirm modal стила

## [1.15.18] — 2026-06-25
requires: 1.15.17

### Fixed
- CSP: добавени `https://fonts.googleapis.com` към `style-src` и `https://fonts.gstatic.com` към `font-src` — Tailwind v4 `@import` в compiled CSS беше блокиран
- Alpine.js Collapse plugin (`@alpinejs/collapse`) добавен към `app.js` — `x-collapse` на Updates страница и accordion компоненти вече работи

## [1.15.17] — 2026-06-25
requires: 1.15.16

### Fixed
- GitHubZipUrl validation rule now accepts release asset URLs (`https://github.com/{owner}/{repo}/releases/download/...`) — previously only `api.github.com` zipball URLs passed, causing install POST to return 302 HTML redirect instead of JSON → "Unexpected token '<'" error in JS

### Tests
- UpdatePageTest: 3 new tests — accepts zipball_url, accepts release asset URL, rejects different repo

## [1.15.16] — 2026-06-25
requires: 1.15.15

### Fixed
- Custom-data inline modals now match confirm modal visually: rounded-2xl (was 3xl), border-gray-200, shadow-2xl (was xl), overlay bg-gray-900/60 (was backdrop-blur), x-transition на overlay и panel

### Tests
- ConfirmModalTest: обновени assertions за новия стил (rounded-2xl border-gray-200, not rounded-3xl)

## [1.15.15] — 2026-06-25
requires: 1.15.14

### Fixed
- Updater: prefer release asset ZIP (`dravion-vX.Y.Z.zip`) over `zipball_url` — zipball lacks `public/build` (gitignored), asset has CI-compiled assets
- `config/updater.php` CLAUDE.md: corrected stale note claiming `public/build` is in protected_paths (it is not)

### Tests
- UpdaterServiceTest: 3 new tests — prefers asset over zipball, falls back when no asset, ignores non-dravion assets

## [1.15.14] — 2026-06-24
requires: 1.15.13

### Fixed
- Confirm modal (global) — light theme fix: bg-white dark:bg-gray-900, text-gray-800 dark:text-white/90, Cancel border-gray-300 dark:border-gray-700

### Tests
- ConfirmModalTest.php (9 теста) — пин на светла/тъмна тема структура на модалите

## [1.15.13] — 2026-06-24
requires: 1.15.12

### Fixed
- Modal header pr-14 padding — заглавие вече не се скрива зад X бутона (всички 4 модала)
- Field action бутони icon-only — "Редактирай" текст не overflow-ва 120px колоната
- ACTIONS колона 200px → 120px (2 icon бутона = ~100px)
- ЗАДЪЛЖИТЕЛНО/ВИДИМО хедъри overflow:hidden;white-space:nowrap

### Tests
- 3 layout regression теста добавени в CustomDataTest (pr-14, width:120px, edit route present)

## [1.15.12] — 2026-06-24
requires: 1.15.11

### Security
- #38: role:admin middleware на license POST/DELETE маршрути
- #39: suspend login — generic error (no credential oracle) + ActivityLog
- #40: 2FA verify — session regenerate() ПРЕДИ Auth::login() (fixation fix)
- #41: HtmlSanitizer — strip style= от всички елементи (CSS injection)
- #42: Webhook 401 — махнато "secret not configured" от response body

## [1.15.11] — 2026-06-24
requires: 1.15.10

### Fixed
- Modal overflow/margin — max-h-[90vh] + overflow-y-auto на всички 4 модала

## [1.15.10] — 2026-06-24
requires: 1.15.9

### Fixed
- Modal Cancel/Save бутони вече имат иконки (checkmark + X)
- ACTIONS колона 160px -> 200px (побира 2 бутона с иконки без overflow)

## [1.15.9] — 2026-06-24
requires: 1.15.8

### Added
- Profile page shows/saves custom fields (ProfileController + view rewrite)
- 2 new tests: test_profile_page_shows_custom_fields, test_profile_custom_field_value_saved

### Fixed
- Removed nonexistent fields from ProfileController update validation

## [1.15.8] — 2026-06-24
requires: 1.15.7

### Fixed
- **Field reorder стрелки** — `<table>/<tbody>/<tr>` не поддържат CSS `order`; заменено с `display:flex;flex-direction:column` контейнер от `<div>` редове → стрелките вече преподреждат визуално

### Changed
- **Бутони** — заменени с `<x-ta.button>` (`variant="primary"/"outline"`, `size="sm"`) с иконки (plus/edit/trash) от `/admin/ui/buttons` системата

## [1.15.7] — 2026-06-24
requires: 1.15.6

### Fixed
- **Custom Data UI revert** — върнат дизайнът от v1.15.5 (raw Tailwind HTML — работещ и визуално коректен)

## [1.15.6] — 2026-06-24
requires: 1.15.5

### Changed
- **Custom Data UI** — бутони → `<x-ui.button>`, inputs → `<x-ui.input>`, textareas → `<x-ui.textarea>`, selects → `<x-ui.select>`, checkboxes → `<x-ui.checkbox>`, badges → `<x-ui.badge>`; модалните контейнери (Alpine scope) остават непроменени; без промяна на функционалността

## [1.15.5] — 2026-06-24
requires: 1.15.4

### Fixed
- **Redesign rollback** — върнат оригиналния дизайн на `/admin/custom-data` (inline Alpine modals от v1.15.1) — бутоните и визуалното оформление са идентични на преди v1.15.3
- **Alpine scope fix** в Add Field modal — `x-data="{fieldType:'text'}"` на `<form>` вместо `x-data` на `<select>` → `x-show` за options работи коректно
- **options_en + options_bg** полетата добавени в старата view за Add Field и Edit Field (select + checkbox)

## [1.15.4] — 2026-06-24
requires: 1.15.3

### Fixed
- **`updateField` трие options** при edit без промяна на options_en (empty string → `isset` = true → options се нулираха); fix: `!empty(trim(...))`
- **`updateCategory` без guard** — system категории можеха да се преименуват; fix: `abort(403)` ако `is_system`
- **`storeField` без guard за `account` категория** — полета добавени там никога не се рендират; fix: `abort(403)` ако `$cat->key === 'account'`
- **`UserController::update()` трие phone/country/city_state** при save на потребител когато system полето е invisible (полето не се рендира → null в request → колоната се нулира); fix: `sometimes|nullable` validation rule

### Added
- 9 нови регресионни теста: options preserved, system category cannot be updated, account category guard, cascade delete, field value idempotency, invisible field hidden, non-admin access denied

## [1.15.3] — 2026-06-24
requires: 1.15.2

### Added
- **Multilingual options** за `select` и `checkbox` полета: опциите се съхраняват като `[{en, bg}]`; в user edit формата се рендират с локала на приложението
- `checkbox` тип вече поддържа множество опции (като select); стойностите се пазят като comma-separated string
- `options_en` + `options_bg` textarea в add/edit field модала (ред по ред, по позиция)
- 5 нови теста: multilingual options store/render, checkbox multi-option, checkbox values save

### Changed
- **Редизайн на `/admin/custom-data`**: `<x-ui.dialog>` за всички модали (trigger+content в един Alpine scope → вече работят правилно); `<x-ui.button>` + `<x-ui.badge>`; table стил като `users/index`; reorder стрелки се показват само при hover

## [1.15.2] — 2026-06-24
requires: 1.15.1

### Fixed
- **Custom Data modals**: "Add Category" button не отваряше модал — заменен `<x-ui.modal>` (без event listener) с inline Alpine `x-data="{open:false}"` за всички модали в `/admin/custom-data`
- All modal interactions (add/edit/delete category, add/edit/delete field) работят правилно

## [1.15.1] — 2026-06-24
requires: 1.15.0

### Added
- **Custom Data reorder**: up/down arrows on categories and fields; order persists via `sort_order`; reflected immediately in user edit form and future front-end usage
- `POST /admin/custom-data/categories/reorder` + `POST /admin/custom-data/fields/reorder` JSON endpoints (admin-only)
- 4 new tests covering category/field reorder, auth guard, and sort_order display order

## [1.15.0] — 2026-06-24
requires: 1.14.2

### Added
- **Custom Data module** (`/admin/custom-data`): admin can create custom field categories and fields for users
  - System categories: Personal Information (phone), Address (country, city/state) — cannot be deleted
  - Account category (name, email, password, role) — immutable, hardcoded
  - Custom categories/fields: text, textarea, select, checkbox types; EN + BG labels; visible/required toggles
  - Field values stored in `user_field_values` table (entity-based, one row per user+field)
  - Fields rendered dynamically in user edit form, grouped by category
  - Admin can add/edit/delete non-system categories and fields
- **3 new DB migrations**: `custom_categories`, `custom_fields`, `user_field_values`
- **3 new models**: `CustomCategory`, `CustomField`, `UserFieldValue`
- **`CustomDataSeeder`**: seeds system categories + fields idempotently on every install/seed

### Changed
- **User personal info**: removed Bio, Postal Code, Tax ID fields and Social Networks section from user edit form — replaced by Custom Data module
- **`User#[Fillable]`**: cleaned up (bio, postal_code, tax_id, social URLs removed)
- **`CustomDataSeeder`** added to `DatabaseSeeder` and `TestCase` base

## [1.14.2] — 2026-06-23
requires: 1.14.1

### Security
- **CI SHA pinning**: GitHub Actions pinned to commit SHA instead of mutable tags (`actions/checkout`, `setup-php`, `setup-node`, `action-gh-release`) — prevents supply chain compromise via tag force-push on a workflow with `contents: write`
- **User model**: `two_factor_secret` + `two_factor_confirmed_at` removed from `#[Fillable]` — no longer mass-assignable; internal callers use `forceFill()` instead
- **2 new unit tests** in `UserModelSecurityTest` verifying 2FA fields reject mass assignment

## [1.14.1] — 2026-06-23
requires: 1.14.0

### Added
- **2FA challenge checkbox**: "Trust this device for N days" checkbox shown on challenge page only when `2fa_remember_days > 0`; cookie set only when checkbox is checked
- **User control**: previously cookie was set automatically on every verify; now user explicitly opts in per-login

### Changed
- `TwoFactorController::verify()` — cookie only when `remember_device` checkbox submitted
- `TwoFactorController::challenge()` — passes `$rememberDays` to view
- 4 new tests in `TwoFactorRememberTest` (checkbox show/hide, cookie with/without checkbox)

## [1.14.0] — 2026-06-23

### Added
- **2FA Remember Device**: after a successful TOTP verify, set an encrypted cookie that skips the 2FA challenge on future logins from the same device
- **Setting `2fa_remember_days`**: admin can configure 0 / 30 / 60 / 90 day retention; 0 disables the feature
- **Logout clears cookie**: `Cookie::forget('dravion_2fa_{id}')` queued on logout
- **7 new tests** in `TwoFactorRememberTest` covering verify-sets-cookie, no-cookie-when-disabled, bypass on login, challenge-without-cookie, challenge-when-disabled, logout-clears-cookie

## [1.10.96] — 2026-06-22
requires: 1.10.92

### Fixed
- **CSP missing `'unsafe-eval'`**: Admin stuck on loading spinner — Alpine.js v3 uses `new Function()`, blocked by CSP. Added `'unsafe-eval'` to SecurityHeaders middleware.
- **Tests**: `SecurityHeadersTest` — 5 tests verify CSP headers, `'unsafe-eval'`, `'unsafe-inline'`, `frame-ancestors 'none'`, admin 200.

## [1.10.95] — 2026-06-22
requires: 1.10.92

### Fixed
- **GitHub Actions**: Reverted SHA-pinned action refs back to version tags (`@v4`, `@v2`) — the SHAs used in v1.10.93/94 were incorrect and broke CI for both releases. SHA pinning requires verified hashes; reverted to tags until they can be verified via `gh api`.
- This release contains all changes from v1.10.93 and v1.10.94 (CSO fixes, arch improvements, localized settings, header tagline display).

## [1.10.94] — 2026-06-22
requires: 1.10.93

### Added
- **Localized settings**: Broadcast Banner, Header Tagline, Footer Text, and Copyright Text now support both English and Bulgarian. EN/BG tab switcher in the settings form. Display layer (`Setting::getLocalized()`) serves the appropriate locale with fallback to the EN value.
- **`Setting::getLocalized($key)`**: New helper — reads `{key}_{locale}` first, falls back to `{key}` (EN default).
- **`<x-ui.lang-tabs>`**: Reusable Alpine.js EN/BG tab switcher component.

## [1.10.93] — 2026-06-22
requires: 1.10.92

### Security
- **CVE fixes**: `composer update guzzlehttp/guzzle guzzlehttp/psr7` — patched CVE-2026-55767 (CRLF injection), CVE-2026-55568 (HTTPS downgrade), CVE-2026-55766 (cookie bypass).
- **CSP header**: Added `Content-Security-Policy` header to `SecurityHeaders` middleware.
- **Webhook hardening**: `UpdateWebhookController` now returns 401 when `GITHUB_WEBHOOK_SECRET` is not configured — prevents unauthenticated cache poisoning.
- **SMTP test**: Raw exception message no longer returned to browser; full error logged server-side, generic `settings.smtp_test_fail` returned to client.
- **GitHub Actions**: All action refs pinned to full commit SHAs (A08 Software Integrity Failures).

### Architecture
- **`AvatarServiceInterface`**: Extracted interface at `app/Contracts/AvatarServiceInterface.php`; bound in `AppServiceProvider`; `AvatarService::store()` converted from static to instance method; `ProfileController` and `UserController` now inject via interface.
- **`UpdateController::redactZipUrls()`**: Eliminated duplicated ZIP URL redaction code from `index()` and `check()` — extracted to private method.
- **`SettingsController::settingSchema()`**: Extracted 19-key settings map to single private method — single source of truth for setting keys and defaults.

### Tests
- Updated `UpdateWebhookTest` to use HMAC-signed requests by default; added `test_no_secret_configured_rejects_request_with_401`.
- Updated `AvatarServiceTest` and `StorageLinkTest` to instantiate `AvatarService` (no longer static).

## [1.10.92] — 2026-06-22
requires: 1.10.91

### Fixed
- Added missing `permissions.manage languages` translation (en + bg).

## [1.10.91] — 2026-06-22
requires: 1.10.87

### Fixed
- Flatpickr locale and week start definitive fix: `window.fpConfig` is now built entirely in the inline `<script>` tag (server-side PHP), not in `app.js`. Bulgarian month/day names are rendered directly from Blade. This eliminates dependency on the JS bundle being current — the fix works with any version of `public/build`.

## [1.10.90] — 2026-06-22
requires: 1.10.87

### Fixed
- Flatpickr locale root cause: `window.appLocale` now reads `session('locale') ?? Setting::get('default_language')` instead of `app()->getLocale()`. Previously, if the admin user had `locale='en'` in their profile, it silently overrode the site's default_language setting — flatpickr always showed English regardless of what was set.
- Settings language selector showed garbled text ("Ð'ÑŠÐ»Ð³Ð°Ñ€ÑÐºÐ¸") — fixed UTF-8 encoding.

### Tests
- Added `test_default_language_setting_bg_overrides_user_profile_locale_en` — catches the exact production bug where user profile locale overrides the site setting.

## [1.10.89] — 2026-06-22
requires: 1.10.87

### Fixed
- SettingsController: removed UTF-8 BOM that caused fatal PHP error on CI.

## [1.10.88] — 2026-06-22
requires: 1.10.87

### Fixed
- Settings: language selector "Bulgarian" label was garbled (ISO-8859-1 bytes instead of UTF-8). Now shows correctly as "Български".

## [1.10.87] — 2026-06-22
requires: 1.10.86

### Fixed
- Flatpickr locale and first-day-of-week now applied via explicit `locale: window.fpConfig` on every instance instead of unreliable `flatpickr.localize()`. Affects `<x-form.date-picker>`, activity log date filters, and the statistics chart date range.

### Tests
- Added `DatePickerLocaleTest` (8 tests) — verifies `window.appLocale` and `window.appFirstDayOfWeek` are correctly output in HTML, and that activity page uses flatpickr inputs (not native `<input type="date">`).

## [1.10.86] — 2026-06-22
requires: 1.10.85

### Fixed
- `public/build` removed from `protected_paths` — CI builds JS/CSS assets and includes them in the release ZIP, so they must be updated on install. Without this fix, flatpickr locale and all JS changes never reached production.

## [1.10.85] — 2026-06-22
requires: 1.10.84

### Added
- Settings: "First Day of Week" (Monday/Sunday) — controls all flatpickr date pickers
- Flatpickr locale now correctly applied (BG/EN) from `window.appLocale` before Alpine init
- `window.appFirstDayOfWeek` exposed from admin layout, read from `week_start` setting

## [1.10.84] — 2026-06-22
requires: 1.10.83

### Fixed
- Date pickers in Activity Log now use app locale (not browser OS locale) — replaced native `<input type="date">` with Flatpickr
- Flatpickr global locale set from `window.appLocale` (BG locale loaded when app language is Bulgarian)

## [1.10.83] — 2026-06-22
requires: 1.10.82

### Fixed
- updates/index.blade.php: `end()` called on inline array expression causes PHP 8 Error — assign to variable first before calling `end()`

## [1.10.82] — 2026-06-22
requires: 1.10.81

### Fixed
- ReleaseInstaller: run `route:clear` after every update so stale route cache does not hide newly added routes (caused `Route [admin.license] not defined` 500 on production after updates)

## [1.10.81] — 2026-06-22
requires: 1.10.80

### Fixed
- ReleaseInstaller: run `lang:seed` after every update so new translation keys are seeded to the database (missing keys from v1.10.77 could cause 500 on check update page)
- UpdateController::index(): wrap checkForUpdate() in try-catch — returns empty update state instead of 500 on unexpected errors
- UpdateController::check(): wrap in try-catch — returns JSON error instead of 500
- UpdaterService::checkForUpdate(): early return (GitHub unreachable) now includes `next_installable` and `older` keys for blade compatibility

## [1.10.80] — 2026-06-20
requires: 1.10.79

### Fixed
- CI: `UpdatePageTest::licensed()` used `DRV-VALID` key which requires `license.cache` for `isValid()` — switched to `DEV-TEST` key (domain-only check, no cache file needed). Fixes `test_page_shows_only_next_version_when_multiple_pending` failure in clean CI environments.

## [1.10.79] — 2026-06-20
requires: 1.10.78

### Fixed
- Activity log: `LicenseController` was using wrong `descKey` (`activity.license_activated`) instead of `activity.log.license_activated` — license events now show translated descriptions
- Activity log event badges now translated via `activity.events.*` keys (en + bg) instead of raw event strings

## [1.10.78] — 2026-06-20
requires: 1.10.75

### Fixed
- Corrected `requires:` chain: v1.10.76 was never released as a GitHub Release (CI failure), so v1.10.77 was blocked for users on v1.10.75. Chain now points to v1.10.75 — the last published release before this one.

## [1.10.77] — 2026-06-20
requires: 1.10.75

### Added
- **Sequential update chain enforcement**: each GitHub release can declare `requires: X.Y.Z` in its release body (parsed from CHANGELOG). `UpdaterService` marks releases as `blocked` if `requires > currentVersion`. `UpdateController::install()` validates and returns 422 if the chain is broken.
- UI shows "Update chain blocked" warning with the missing prerequisite version instead of the install button
- JS filters blocked releases from the install queue; passes `requires` in the install payload
- 10 new tests across `UpdaterServiceTest` and `UpdatePageTest` covering chain logic, blocked releases, satisfied requires, null requires

### Changed
- CHANGELOG format now includes `requires: prev_version` line after version header — mandatory for every new release

## [1.10.76] — 2026-06-20

### Fixed
- **Sidebar UPDATE badge now shows automatically**: `MenuHelper` now auto-fetches the latest GitHub release when `github_latest_version` cache is empty (no scheduler/webhook required); failed fetches are backed off for 5 minutes to avoid hammering GitHub
- **Cron command moved to Settings page** (not Dashboard) — visible under Scheduler section below the settings form with status indicator and copy button

### Added
- 3 new tests in `UpdateNavBadgeTest`: auto-fetch on cache miss, no badge on GitHub 503, no auto-fetch when owner not configured
- `github_check_failed` cache key (5 min TTL) prevents hammering GitHub on repeated cache misses

## [1.10.75] — 2026-06-20

### Added
- **Scheduler status card on dashboard**: shows last run time + copyable cron command so admins know exactly what to add on shared hosting; green tick when scheduler is detected, warning when not
- `updates:check-releases` now also writes `scheduler_last_run` cache key (TTL 8h) for status detection
- `app.copy` / `app.copied` + `dashboard.scheduler*` translation keys in en + bg

## [1.10.74] — 2026-06-20

### Fixed
- **Sidebar "UPDATE" badge now appears automatically**: added `updates:check-releases` Artisan command that fetches the latest GitHub release and writes `github_latest_version` to cache; scheduled every 4 hours via Laravel scheduler — badge no longer requires a webhook or a manual visit to the Updates page

### Added
- `app/Console/Commands/CheckReleasesCommand.php` — `php artisan updates:check-releases`
- Schedule in `routes/console.php`: `everyFourHours()`
- 5 new tests in `CheckReleasesCommandTest` covering cache write, version stripping, unreachable GitHub, and missing config

## [1.10.73] — 2026-06-20

### Fixed
- **"Check again" now re-checks both license and updates**: replaced plain anchor links with POST forms pointing to new `POST /admin/updates/check-all` route; action clears `license.cache` and `github_latest_version` cache so LicenseCheck middleware does a live ping and `index()` fetches fresh GitHub releases
- `index()` now writes `github_latest_version` cache after every GitHub fetch, keeping sidebar badge in sync without needing a webhook

### Added
- `POST /admin/updates/check-all` route (`admin.updates.check-all`) and `checkAll()` controller method
- 7 new tests in `CheckAllTest` covering auth guards, cache clearing, flash message, and blade form rendering

## [1.10.72] — 2026-06-20

### Changed
- **One version per click**: Updates page now installs exactly one version per button press; shows only the next (oldest) pending version with its changelog; displays "+N more pending" badge when multiple updates are queued; on successful install reloads the page to show the next version
- `zip_url` validation now explicitly requires `string` type to prevent batch payloads
- 3 new tests in `UpdatePageTest` covering single-version display, pending count badge, and foreign-host URL rejection

## [1.10.71] — 2026-06-20

### Added
- **Updates nav badge**: pulse green dot + "UPDATE" badge in sidebar next to Updates link when a newer version is available in cache (`github_latest_version`)
- 8 new tests in `UpdateNavBadgeTest` covering badge presence/absence, version comparison, v-prefix handling, non-admin exclusion, and blade rendering

## [1.10.70] — 2026-06-20

### Added
- **Check License button** on Updates page: replaces "Go to License" link with a POST form that clears `storage/license.cache` and does a live license ping; redirects with success flash if license is now active, or warning flash if not
- `POST /admin/updates/check-license` route (`admin.updates.check-license`)
- `UpdateController::checkLicense()` method
- 7 new tests in `CheckLicenseTest` covering auth, role guard, stale cache bypass, success and warning redirects, and blade button presence

## [1.10.59] — 2026-06-20

### Fixed
- `GET /admin/license` now redirects to `admin.updates` — combined License & Updates page is the single entry point; separate license-only page no longer exists as a destination

## [1.10.58] — 2026-06-20

### Fixed
- Updates+license page: activate/remove license form now redirects back to `/admin/updates` instead of `/admin/license` — combined page stays intact after license actions
- Updates page: reverted to `isValid()` (cache, fast) on page load; live check stays only in `install()` and `check()` endpoints
- Admin layout: license blur no longer applies on `/admin/license` route (content was blurred/unusable without a key)
- `LicenseController`: `_back` hidden field (whitelist-validated) controls redirect target; unknown values fall back to `admin.license`

## [1.10.57] — 2026-06-20

### Added
- GitHub release webhook (`POST /webhook/github/releases`) — GitHub pushes notification when a Release is published; admin header automatically shows a version badge without requiring "Check Again"
- `GITHUB_WEBHOOK_SECRET` env var for HMAC-SHA256 signature verification of webhook payloads
- `updates.new_version_available` translation key (EN + BG)
- 8 `UpdateWebhookTest` tests covering signature verification, event filtering, cache behavior
- 3 `LicensePageTest` flash message tests verifying `license_activated` ≠ `license_removed` in both locales

### Changed
- Updates page (`/admin/updates`) now uses `isValidLive()` — live license check on every page load catches suspended/revoked keys before showing download URLs

### Security
- Webhook HMAC-SHA256 verified via `hash_equals()` — rejects tampered or unsigned payloads when secret is configured

## [1.10.56] — 2026-06-20
### Fix
- Add missing GET /dashboard route + DashboardController + view for regular users (fixed Route [dashboard] not defined in 7+ tests)
- Add missing GET /admin/license route + LicenseController::show() + admin/license.blade.php (fixed Route [admin.license] not defined in 10+ tests)
- LicenseController::update() + remove() redirect to route('admin.license') instead of back() (fixed redirect assertions in tests)
- Storage route realpath() path-traversal check: use realpath($base) for comparison to handle mixed Windows separators (fixed 2 StorageSymlinkTest 404s)
- UpdaterServiceTest: use app(UpdaterService::class) instead of new UpdaterService() (fixed 7 constructor errors)
- Result: 583/585 tests pass (2 skipped = Windows-only symlink creation)

## [1.10.55] — 2026-06-20
### Security / Arch
- License security: isValidLive() + verifyNow() — real-time server ping on update check and install (blocks suspended/revoked licenses even with valid 24h cache)
- UpdateController::check() hides zip_url when license invalid; install() aborts 403 immediately
- LicenseService: fail-open on server unreachable (cached=valid → allow; no cache → block)
- ActivityLogger Facade (App\Facades\ActivityLogger) — proper Laravel Facade replaces broken __callStatic pattern (PHP 8.3 incompatible)
- All 7 callers migrated from App\Services\ActivityLogger to App\Facades\ActivityLogger
- NullActivityLogger bound in tests via AppServiceProvider — no stray DB writes in test suite
- 56 new tests in LicenseSecurityTest: isValid, verifyNow, isValidLive, critical activate→suspend→block scenario, check/install endpoints, activate(), cache integrity (HMAC tamper), access control

## [1.10.54] — 2026-06-20
### Arch
- AvatarService: guard imagecreatefromstring() false return — throw RuntimeException instead of fatal error on corrupt upload (Candidate 6)
- DomainHelper::maskKey(): dedup mask() from LicenseController + UpdateController — single source of truth (Candidate 7)
- UpdateController: use DomainHelper::maskKey(), remove private mask()
- LicenseController: remove dead private mask() method

## [1.10.53] — 2026-06-20
### Arch
- Extract DomainHelper (app/Support/DomainHelper.php): isDevDomain() + fromAppUrl() — eliminates duplicated private method in LicenseService and LicenseCheck
- LicenseService + LicenseCheck: replace private isDevDomain() with DomainHelper::isDevDomain()

## [1.10.52] — 2026-06-20
### Arch
- Setting::get() request-scoped static cache — eliminates repeated DB queries per request (31 call-sites, up to 20 per admin settings page)
- Setting::set() / setMany() invalidate cache on write — no stale reads
- Setting::flushCache() helper for test isolation between assertions
- app/Support/Settings.php: typed constants for all 20+ known setting keys

## [1.10.51] — 2026-06-20
### Arch
- ActivityLogger: static → injectable via ActivityLoggerInterface contract
- Static callers unchanged (__callStatic delegates to container) — zero diff in 9 caller files
- NullActivityLogger (no-op): bind in tests to skip DB; eliminates spatie activitylog dependency from unit tests
- AppServiceProvider: bind ActivityLoggerInterface → ActivityLogger (singleton-style via container)
- UpdaterServiceSortTest: fix constructor call after UpdaterService became DI-injected

## [1.10.50] — 2026-06-20
### Arch
- Decompose UpdaterService God object: extract ReleaseDownloader, ReleaseInstaller, UpdateHistory into app/Services/Updater/
- UpdaterService becomes thin orchestrator (~80 lines, was 363) delegating to focused collaborators
- UpdateHistory: 4 unit tests covering append/accumulation/empty-changelog
- Each collaborator has single responsibility — testable in isolation without mocking Laravel internals

## [1.10.49] — 2026-06-18
### Arch
- Extract HtmlSanitizer service (app/Services/HtmlSanitizer.php) from PagesController private method — 17 unit tests, covers XSS/CSS-exfil/javascript:/data: vectors
- PagesController: inject HtmlSanitizer, remove 80-line embedded sanitizeContent()/domSanitize() methods

## [1.10.48] — 2026-06-18
### Fixed
- GitHub Actions: revert softprops/action-gh-release to @v2 tag — SHA was incorrect and broke CI

## [1.10.47] — 2026-06-18
### Security
- PagesController::domSanitize(): strip url()/expression()/behavior()/vbscript() from style attributes — blocks CSS data exfiltration
- SecurityHeaders middleware: X-Frame-Options DENY, X-Content-Type-Options nosniff, Referrer-Policy, X-XSS-Protection, Permissions-Policy on all web responses
- GitHub Actions: SHA-pinned actions/checkout, actions/setup-node, softprops/action-gh-release — prevents tag-hijack supply chain attack
- LicenseCheck: log warning when license server unreachable (fail-open remains, now traceable)

## [1.10.46] — 2026-06-18
### Fixed
- Portal unlicensed banner: was added to wrong file (welcome.blade.php); public portal uses layouts/public.blade.php — banner now correctly in that layout, visible on all public pages
- Admin app-header: globe icon (View Site) now opens in same window instead of new tab

## [1.10.45] — 2026-06-18
### Fixed
- welcome.blade.php (public portal page at /dravion/): add unlicensed warning banner — this page is standalone HTML and does not use x-layouts.portal, so the banner had to be added directly here

## [1.10.44] — 2026-06-18
### Fixed
- CRITICAL: MenuHelper.php called route('admin.license') which no longer exists — caused 500 on every page after v1.10.42

## [1.10.43] — 2026-06-18
### Changed
- Admin layout: blur reduced to 2px; license check now reads config() directly instead of session — works on all pages including /sessions and /api-tokens which bypass license.check middleware
- Portal layout: thin warning bar when no license key is configured
- Added license.no_license_portal translation key (EN + BG)

## [1.10.42] — 2026-06-18
### Removed
- `/admin/license` GET route and `admin/license.blade.php` view — license management is now fully on the License & Updates page
- `LicenseController::show()` method removed (POST/DELETE still exist for form actions)
### Changed
- All links to `admin.license` updated to `admin.updates`: app-header badge, updates page "Go to License" button
- Blur exception updated to `admin.updates` only (no longer needs to exclude the removed license page)

## [1.10.41] — 2026-06-18
### Changed
- Admin layout: page content blurs (filter:blur 4px, pointer-events:none) when license_warning is active, except on License & Updates and License pages where the admin can actually fix the issue

## [1.10.40] — 2026-06-18
### Fixed
- License activate/remove now log to activity log (category: license, events: activated/removed)
- License warning banner now shows in admin layout when session('license_warning') is set — previously the warning was flashed to session but never displayed anywhere; banner links directly to License & Updates page
- Added activity translation keys: license_activated, license_removed (EN + BG)

## [1.10.39] — 2026-06-18
### Changed
- Updates page renamed to "License & Updates": replaced small license link card with full license management UI (status, key input, activate/remove); layout changed from 3-col to 2-col; LicenseController now uses redirect()->back() so form works from both the license page and the updates page

## [1.10.38] — 2026-06-18
### Changed
- Settings page: 2-column grid layout (General+System+ActivityLog left, PublicSite+Logo+Email+License right) — reduces page height ~50%

## [1.10.37] — 2026-06-18
### Fixed
- Logo and avatar images: replaced all Storage::url() calls in views with url('storage/'.$path) — url() uses Symfony SCRIPT_NAME detection (same as route generation, proven correct) and is unaffected by Apache system env APP_URL override; fixed in 12 view locations across sidebar, header, public layout, user-dropdown, profile-card, dashboard, settings, users pages
- AppServiceProvider: removed risky URL::forceRootUrl() call; storage disk URL now fixed using url('storage') which uses the same correct mechanism as route generation

## [1.10.36] — 2026-06-18
### Fixed
- Storage image URLs (logo, avatars): use request()->root() from Symfony HttpFoundation instead of env('APP_URL') — bypasses Apache system env var that overrides .env subdirectory path; applies to both URL::forceRootUrl() and filesystems.disks.public.url

## [1.10.35] — 2026-06-18
### Removed
- `/dashboard` route, `dashboard.blade.php` view, and all `route('dashboard')` references — portal is `/` (home), admin panel is `/admin/dashboard`

## [1.10.34] — 2026-06-17
### Fixed
- Storage images: root cause found — Apache exports APP_URL system env var without subdirectory path; Laravel Dotenv::createImmutable() keeps system env and ignores .env, so config('app.url') and getenv() both return wrong host-only URL; fixed by resolveAppUrl() which reads SCRIPT_NAME from live HTTP request (always correct) and falls back to reading .env file directly

## [1.10.33] — 2026-06-17
### Fixed
- Storage images: call `Storage::forgetDisk('public')` after overriding the disk URL in AppServiceProvider — FilesystemManager caches the adapter with URL baked in on first access, so config() change alone had no effect on already-created adapters

## [1.10.32] — 2026-06-17
### Fixed
- Updater: reverted to old design (Blade conditionals, all changelogs visible); sequential AJAX install still active — button installs all pending versions oldest-first, no page reload; "up to date" state shown inline when done
- diag.php v3: fixed `define(LARAVEL_START)` error; proper Laravel boot for accurate Storage::url() diagnostics

## [1.10.31] — 2026-06-17
### Changed
- Updater: installs versions one at a time (oldest first) instead of jumping to latest; each install is AJAX — no full page reload; pill badges show progress (step N of M); after each success the next version is shown with its own Install button; when all done the panel switches to "up to date" state without reload

## [1.10.30] — 2026-06-17
### Security
- CRITICAL: Path traversal in `GET /storage/{path}` — `realpath()` + prefix check now blocks `../../` escapes from `storage/app/public/`
- HIGH: CMS XSS — replaced `strip_tags()` (allowed event handlers on permitted tags) with DOMDocument sanitizer that explicitly whitelists attributes per tag and strips all `on*` handlers and `javascript:`/`data:` URI schemes
### Fixed
- Storage images: `AppServiceProvider` now overrides `filesystems.disks.public.url` from live env (via `getenv()`) — works even when config cache has stale APP_URL baked in
- Storage images: `index.php` clears `bootstrap/cache/config.php` when APP_URL is auto-corrected — next request starts fresh

## [1.10.29] — 2026-06-17
### Performance
- Cache DB translations 24h per locale+group → eliminates repeated DB queries on every page load (main TTFB fix)
- Bust translation cache automatically when admin saves translations via LanguageController
- Preload Onest woff2 in `<head>` → font starts loading in parallel with CSS, not after
- Remove unused Inter Google Font from admin layout (Onest is the project font)
- Fix sidebar CLS: inline script reads localStorage before Alpine initialises, sets `--sidebar-init-w` CSS var → sidebar renders at correct width immediately, no layout shift

## [1.10.28] — 2026-06-17
### Fixed
- Storage images actual root cause: `APP_URL` in `.env` was `https://apsbg.com` (missing `/dravion` subdirectory) → `Storage::url()` generated wrong URLs pointing to the main site instead of the Dravion subdirectory; `index.php` now auto-detects and self-heals `APP_URL` when the stored value is missing the install subdirectory

## [1.10.27] — 2026-06-17
### Fixed
- Storage images root cause fix: root `.htaccess` was rewriting `storage/xxx` → `public/storage/xxx`, causing Apache to restart with a new path that made Laravel see `REQUEST_URI = /dravion/public/storage/xxx` — no route matches, 404. Fix: removed `storage/.+` from root `.htaccess` rewrite; all storage requests now fall directly to `index.php` → Laravel `storage.serve` route serves the file correctly

## [1.10.26] — 2026-06-17
### Fixed
- Storage images: removed `serve:true` from public disk — it was registering a framework route that shadowed the explicit `GET /storage/{path}` route in web.php; now only the web.php route exists and correctly serves files from `storage/app/public/`

## [1.10.25] — 2026-06-17
### Fixed
- Storage auto-heal on every boot (`AppServiceProvider`): detects broken `public/storage` symlink → removes it → recreates (absolute then relative fallback); broken symlink caused Apache to 404 before reaching PHP
- `.htaccess`: force `/storage/` paths through PHP regardless of symlink state
- 4 new tests: broken symlink removed, serve route works, 404 on missing, URL format

## [1.10.24] — 2026-06-17
### Fixed
- Avatar and logo images not loading: added `GET /storage/{path}` PHP route that serves files directly from `storage/app/public/` — guaranteed to work on shared hosting without symlink; Apache's `.htaccess` rewrite falls through to PHP when the symlink is absent

## [1.10.23] — 2026-06-17
### Fixed
- Avatar and logo images not loading on shared hosting: added `serve: true` to `public` filesystem disk — Laravel now serves `storage/app/public` files via built-in route (`GET /storage/{path}`) without requiring a working symlink
- InstallController: fallback relative symlink (`../../storage/app/public`) when `php artisan storage:link` fails (relative symlinks work on most cPanel setups where absolute symlinks fail)
- Removed `serve: true` from `local` disk to prevent route conflict with `public` disk at `/storage`
- Added `StorageLinkTest` covering disk config, `serve:true`, and avatar upload URL

## [1.10.22] — 2026-06-17
### Fixed
- Notification bell dot: uses inline `style` instead of Tailwind classes — Tailwind v4 purges dynamic `:class` bindings; dot now always visible (gray=0 unread, orange+pulsing=unread)
- `markRead` and `markAll`: use `route()` helpers instead of hardcoded `/notifications/...` — fixes broken mark-read on subdirectory hosting (`/dravion/`)
- Unread item background and icon colors: switched to inline styles to survive Tailwind purge

## [1.10.21] — 2026-06-17
### Changed
- Notification bell: always shows a small dot indicator — gray when 0 unread, orange+pulsing when unread > 0

## [1.10.20] — 2026-06-17
### Fixed
- Notification dropdown overflow: use `position:absolute; right:0` (inline style) instead of `-right-[240px] lg:right-0` — Tailwind v4 purges unused responsive variants; dropdown no longer expands the page
- Notification bell badge: reverted to simple pulsing dot (copy-paste from original TailAdmin) — count badge was causing visual issues

## [1.10.19] — 2026-06-17
### Changed
- Notification bell: unread items now have distinct orange background (`bg-orange-50` / dark equivalent) vs read items; unread count badge on bell button (number with pulsing animation, shows `9+` when over 9); each notification item now shows a bell icon area, title+body in TailAdmin style, and `Notification • time` meta row
### Fixed
- Security (MEDIUM): CMS page content sanitized via `strip_tags()` with allowed safe HTML tags before saving — prevents stored XSS when content rendered with `{!! !!}` in public views

## [1.10.18] — 2026-06-17
### Changed
- Notification bell redesigned to match original TailAdmin: `rounded-full` button, filled bell SVG, pulsing orange `animate-ping` dot badge (replaces number badge), dropdown `rounded-2xl` with X close button and unread orange dot per item

## [1.10.17] — 2026-06-17
### Fixed
- `UpdaterService`: read changelog from `CHANGELOG.md` in the extracted ZIP (not from GitHub release body which is often empty) — `detectChangelogFromExtract()` parses the matching `## [version]` section via regex

## [1.10.16] — 2026-06-17
### Fixed
- Update history accordion now stores and displays changelog per entry — JS passes `changelog` alongside `zip_url` on install; stored in `history.json`; view shows "Show changelog" toggle same as before

## [1.10.15] — 2026-06-17
### Fixed
- `UpdaterService::ensureHistoryExists()`: bootstrap fix — if `history.json` doesn't exist but `install.lock` does, seeds the file with the current version on first page load (handles manual ZIP deploys that predate history tracking)
- Update history accordion: entries with `from = —` (bootstrapped) show only the installed version without an arrow

## [1.10.14] — 2026-06-17
### Fixed
- Update history accordion now shows only updates actually installed via the updater (tracked in `storage/app/updates/history.json`), not all older GitHub releases — fresh install shows 0 history entries
- Each history entry: from version → to version + timestamp
- Added `UpdaterService::getUpdateHistory()` and `appendToHistory()` helpers

## [1.10.13] — 2026-06-17
### Fixed
- `UpdaterService::downloadAndInstall()`: after copying files, explicitly reads new version from extracted `config/dravion.php` and writes it to the protected local config — fixes infinite update loop where version never bumped after successful install
- `opcache_reset()` called after update to ensure updated PHP files served immediately

## [1.10.12] — 2026-06-17
### Added
- `InstallSeeder` — central entry point for all installer data seeding; installer calls only this, never anything else
- `DefaultLanguagesSeeder` — seeds en + bg languages via `insertOrIgnore`
- `DefaultSettingsSeeder` — seeds all app settings with defaults via `firstOrCreate` (safe to run on existing data)
- `DefaultPagesSeeder` — seeds home, about, pricing, contact, gallery pages with hero images via `firstOrCreate`
### Changed
- `InstallController::handleFinish()`: replaced manual `RolesAndPermissionsSeeder` + inline language insert with `db:seed --class=InstallSeeder` — installer never needs touching for new default data
### Rule
- New feature shipping default data → create `database/seeders/YourFeatureSeeder.php` + add `$this->call(YourFeatureSeeder::class)` to `InstallSeeder`. Installer stays untouched.

## [1.10.11] — 2026-06-17
### Fixed
- `routes/web.php` `/` route: also check DB connectivity when `install.lock` exists — redirects to `/install` if lock exists but DB is broken/unconfigured
- `InstallController` database step: two-phase flow — Phase 1 tests connection, Phase 2 shows confirmation-only page (no re-entering credentials); `?reset=1` clears session and returns to Phase 1
- `database.blade.php`: separate confirmation view with amber warning, checkbox, "Drop & Reinstall" and "Change credentials" buttons

## [1.10.10] — 2026-06-17
### Fixed
- `index.php`: auto-detect `APP_URL` from `HTTP_HOST` + `SCRIPT_NAME` when value is `http://localhost` placeholder — fixes redirect from `/` to `/install` in subdirectory installs (e.g. `/dravion/`) AND at domain root
- `InstallController` database step: detect existing tables in target DB; require explicit `confirm_drop` checkbox before proceeding; run `migrate:fresh` on finish if tables existed
- `database.blade.php`: show amber warning banner + confirm checkbox when existing tables detected

## [1.10.9] — 2026-06-17
### Fixed
- `routes/web.php` `/` route: removed `Schema::hasTable('settings')` check — it was throwing DB exception when credentials are empty (pre-install); now redirects to `/install` based on `install.lock` only
- `InstallGuard`: if `install.lock` exists but DB connection fails, allow installer to run again — fixes 404 on `/install` when lock file is stale from a broken previous install attempt

## [1.10.8] — 2026-06-17
### Fixed
- `index.php`: if neither `.env` nor `.env.installer` exist, generate a minimal `.env` with a fresh random `APP_KEY` directly — eliminates `MissingAppKeyException` on servers where `.env.installer` is missing or the copy fails silently

## [1.10.7] — 2026-06-17
### Fixed
- `index.php`: generate real random `APP_KEY` via `openssl_random_pseudo_bytes(32)` when missing or empty in `.env` — handles both empty line and missing line cases; sets via `putenv`/`$_ENV`/`$_SERVER` so Laravel picks it up before dotenv runs

## [1.10.6] — 2026-06-17
### Fixed
- `index.php`: auto-create `bootstrap/cache` and `storage/` skeleton dirs at runtime — eliminates "directory must be present and writable" crash on shared hosting where ZIP extraction skips empty directories

## [1.10.5] — 2026-06-17
### Fixed
- `make-full-zip.ps1`: rewritten for PowerShell 5.1 compatibility (removed `?.` null-conditional operator)

## [1.10.4] — 2026-06-17
### Fixed
- `index.php`: inject installer `APP_KEY` when `.env` exists but key is empty — fixes `MissingAppKeyException` on shared hosting if `.env` was uploaded without a key
- `make-full-zip.ps1`: write `.gitkeep` in all skeleton dirs (logs, sessions, views, cache, bootstrap/cache) so ZIP preserves them — fixes `Please provide a valid cache path` on first boot

## [1.10.3] — 2026-06-17
### Fixed
- `.htaccess`: removed complex dynamic RewriteBase detection — Apache handles relative substitutions correctly in subdirectory `.htaccess` automatically; works at domain root AND in `/dravion/` subdirectory

## [1.10.2] — 2026-06-16
### Fixed
- `index.php`: friendly "vendor/ missing" message instead of blank 500 on incomplete upload
- `SetLocale` middleware: catches DB exception before install (settings table doesn't exist yet)
- `MaintenanceMode` middleware: catches DB exception before install
- `.env.installer`: removed SQLite/tmp dependency — uses mysql with empty credentials
- All pre-install requests now survive without crashing before reaching `/install`

## [1.10.1] — 2026-06-16
### Fixed
- Release ZIP now includes `vendor/` and `public/build/` (built by GitHub Actions) — required for shared hosting installation without separate `composer install`
- `make-full-zip.ps1` rewritten: copies to temp via robocopy, runs `composer install --no-dev`, then zips — avoids locked-file issues on Windows
- `UpdaterService::getReleases()` sorts by semver, not GitHub publish date

## [1.10.0] — 2026-06-16
### Added
- Installer tests: 38 tests covering all 5 steps (requirements/database/admin/license/finish), views, validation, session flow, install lock, admin user creation
- `InstallGuardTest`: 8 tests — lock file blocks all routes (404), accessible without lock, invalid step 404
- `bootstrapEnv()` in `InstallController`: auto-creates `.env` from `.env.example` at requirements step, forces `SESSION_DRIVER=file` so install works on shared hosting before DB exists
- Installer: `seedDefaultLanguage()` inserts default English language row on finish
- Installer: `storage:link` attempt on finish (non-fatal on restrictive shared hosting)
- Installer: creates all required `storage/` subdirs on finish (framework/sessions, framework/cache/data, etc.)
- Installer: requires cURL + GD extensions in requirements check
- `writeEnv()`: adds `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `FILESYSTEM_DISK=local`, full `MAIL_*` defaults
### Fixed
- `User` model: added `email_verified_at` to `#[Fillable]` — `firstOrCreate()` now correctly sets it, preventing redirect to email verification on first admin login
- `RegisterController::store()`: added `Setting::get('registration')` check — direct `POST /register` no longer bypasses the registration-disabled toggle
### Security
- Language routes (`/admin/languages/*`) now gated by `can:manage languages` permission (admin-only); previously accessible to any `editor` role
- `manage languages` permission added to `RolesAndPermissionsSeeder` — assigned to `admin` only

## [1.9.0] — 2026-06-16
### Added
- `App\Contracts\LicenseServiceInterface` — contract for DI binding and mockability
- `LicenseService` now implements `LicenseServiceInterface` (instance methods only)
- `AppServiceProvider` binds `LicenseServiceInterface` → `LicenseService` in the container
- `LicenseController`, `UpdateController`, `LicenseCheck` middleware, `InstallController` inject `LicenseServiceInterface` via constructor DI
- Installer requirements check: added cURL and GD extension checks, bumped PHP requirement label to 8.3
- Installer `.env` generation: `APP_NAME` now properly escaped via `EnvWriter::escapeValue()`, added `DRAVION_LICENSE_SERVER` entry
- `config/google2fa.php` added to updater protected paths
### Changed
- All tests updated to use `app(LicenseServiceInterface::class)` instead of static `LicenseService::*` calls

## [1.8.0] — 2026-06-16
### Added
- Two-Factor Authentication (TOTP) with `pragmarx/google2fa-laravel` + `bacon/bacon-qr-code`
- `TwoFactorController`: setup (QR code), confirm, disable, challenge, verify actions
- Login gate: users with 2FA enabled are redirected to TOTP challenge before session is created
- Migration: `two_factor_secret` + `two_factor_confirmed_at` columns on users table
- Views: `auth/two-factor/setup.blade.php`, `manage.blade.php`, `challenge.blade.php`
- Profile page: 2FA card with link to enable/manage
- Lang keys: `auth.2fa_*` (en + bg), `flash.2fa_enabled`, `flash.2fa_disabled`
- TwoFactorTest: 12 tests covering full 2FA lifecycle
### Security
- #21: TOTP 2FA added — eliminates password-only auth risk for admin accounts

## [1.7.0] — 2026-06-16
### Added
- EnvWriter service: atomic `.env` writes with `flock()` — eliminates race condition on concurrent admin requests
- Failed login attempts now logged to activity log (causer + masked email) — A09 Logging Failures fix
- `config/dravion.php` added to updater protected paths — never overwritten by self-update
- SessionManagementTest: 5 tests for session listing and logout-other-devices
- PublicPagesTest: 10 tests for HomeController (home/gallery/CMS pages) and ContactController
### Fixed
- `.env` password escaping: `addslashes()` replaced with `EnvWriter::escapeValue()` — handles `$`, `#`, spaces correctly
- LicenseController and InstallController now use EnvWriter (no more raw `file_put_contents` on `.env`)
### Security
- #36: Failed login attempts logged with email + IP to activity_log
- #37: `.env` write race condition fixed via EnvWriter with exclusive flock()

## [1.6.0] — 2026-06-16
### Added
- TipTap editor: independent scroll on both editor and live preview panes
- TipTap editor: resize handle bar at bottom — drag to resize height (min 200px)
- TipTap editor: HTML source view font increased to 14px
- TipTap preview: `display:flex` via CSS class (`.tiptap-preview-pane`) — fixes Alpine x-show overriding flex scroll
- TipTap auto-scroll: preview scrolls only its own panel (`panel.scrollTop`) not the page
### Fixed
- Preview scroll broken: `x-show` was setting `display:block` over inline `display:flex`, preventing flex scrolling
- Resize handles inside panes were clipped by `overflow:hidden` — moved to sibling resize bar
- `overflow:hidden` on tiptap container now correctly clips both panes while resize bar (sibling) remains accessible

## [1.5.0] — 2026-06-16
### Added
- TipTap editor with live split-pane preview (real-time sync, auto-scroll to cursor position)
- HTML source view with auto-formatting via js-beautify
- Pages permissions: granular `can:` middleware guards on all CRUD routes
- Pages group added to Roles permission matrix (view/create/edit/delete)
- Installer: `app_name` field (was hardcoded "Dravion")
- Installer: try/catch around migrate and seed — user-friendly error on failure
### Fixed
- TipTap buttons not working — Alpine Proxy wrapped editor broke ProseMirror state equality
- TipTap preview: duplicate class= attribute bug fixed
- `hero_cta_url` now validated as `url` (blocks javascript: XSS scheme)
- `footer_copyright` escaped with `{{ }}` instead of `{!! !!}` (stored XSS fix)
- Editor and preview fonts unified: Onest 15px/1.75, cms-content moved to app.css
### Security
- Pages routes: added `can:` middleware per action (view/create/edit/delete)
- concurrently upgraded 9.2.1→10.0.3 (shell-quote CVE GHSA-w7jw-789q-3m8p CVSS 8.1)
- Installer: license field label changed from "(optional)" to required

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
