# tests/

PHPUnit 12 test suite. Run with `composer test` (clears config cache first, uses SQLite in-memory).

## Structure

```
tests/
  TestCase.php              — Base class: seeds RolesAndPermissionsSeeder + flushes Setting::$cache each test
  Feature/
    Admin/                  — Admin panel feature tests (LicenseService, UpdaterService, Users, Roles, etc.)
    Auth/                   — Authentication flows (login, register, password reset, email verification, 2FA)
    Install/                — Installer wizard flow and InstallGuard middleware
    Portal/                 — User portal (PortalDashboardTest)
    ApiTokenTest.php
    DatabaseTranslationTest.php
    ExampleTest.php
    InstallTest.php
    NotificationTest.php
    NotificationTriggersTest.php
    PublicPagesTest.php
    SessionManagementTest.php
    StorageLinkTest.php
    TranslationPagesTest.php
  Unit/
    Components/
      UiComponentsTest.php
    Updater/
      UpdateHistoryTest.php
    EnvWriterTest.php
    ExampleTest.php
    HtmlSanitizerTest.php
    LicenseServiceTest.php
    StorageSymlinkTest.php
    UpdaterServiceSortTest.php
```

## TestCase base class

- Calls `$this->seed(RolesAndPermissionsSeeder::class)` in `setUp()` — roles/permissions available in every test.
- Calls `Setting::flushCache()` in `setUp()` — static property cache doesn't survive DB rollback, so must be reset manually.

## Conventions

- DB: SQLite in-memory (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:` in `phpunit.xml`).
- Test method names: `test_it_does_something()` snake_case, descriptive.
- Use `RefreshDatabase` trait in every test class.
- Swap `ActivityLoggerInterface` → `NullActivityLogger` in tests that trigger logging but don't assert on log DB rows.
- Mock HTTP with `Http::fake()` — never make real network calls in tests.
- Feature tests go in `Feature/`, isolated class/function tests in `Unit/`.
- Group related admin tests under `Feature/Admin/` (one file per domain area, e.g. `LicenseServiceTest`, `UserManagementTest`).

## Running tests

```bash
composer test          # php artisan config:clear && phpunit
php artisan test --filter=LicenseServiceTest
php artisan test tests/Feature/Admin/
```
