<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\GlobalSearchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $lockExists = file_exists(storage_path('install.lock'));
    $dbInstalled = $lockExists || \Illuminate\Support\Facades\Schema::hasTable('settings');
    if (! $dbInstalled) {
        return redirect()->route('install.index');
    }
    // Recreate lock file if missing but DB is installed
    if (! $lockExists) {
        file_put_contents(storage_path('install.lock'), '');
    }
    return app(\App\Http\Controllers\HomeController::class)->index();
})->name('home');

Route::get('/p/{slug}', [\App\Http\Controllers\HomeController::class, 'show'])->name('page.show');

// Installer — disabled after install.lock exists
Route::middleware(\App\Http\Middleware\InstallGuard::class)
    ->prefix('install')->name('install.')->group(function () {
        Route::get('/', [InstallController::class, 'index'])->name('index');
        Route::get('/{step}', [InstallController::class, 'show'])->name('step');
        Route::post('/{step}', [InstallController::class, 'process'])->name('process');
    });

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/register',  [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:3,1');

    Route::get('/forgot-password',  [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email')->middleware('throttle:3,1');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password',        [ResetPasswordController::class, 'store'])->name('password.update')->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::put('/profile/password', [PasswordController::class, 'update'])->name('profile.password');
    Route::patch('/profile/locale', [PasswordController::class, 'updateLocale'])->name('profile.locale');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/locale/{code}', [LocaleController::class, 'switch'])->middleware('auth')->name('locale.switch');

// Email verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
});

// Session management
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/sessions',              [SessionController::class, 'index'])->name('sessions.index');
    Route::post('/sessions/logout-others', [SessionController::class, 'logoutOthers'])->name('sessions.logout-others');
});

// API Tokens (Sanctum)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/api-tokens',       [ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::post('/api-tokens',      [ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::delete('/api-tokens',    [ApiTokenController::class, 'destroyAll'])->name('api-tokens.destroy-all');
    Route::delete('/api-tokens/{id}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');
});

// Notifications
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});

// User dashboard
Route::get('/dashboard', fn () => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

// Admin
Route::middleware(['auth', 'role:admin|manager|editor', 'license.check'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users — guarded by permission
    Route::get('/users',                             [UserController::class, 'index'])->name('users.index')->middleware('can:view users');
    Route::get('/users/export',                      [UserController::class, 'export'])->name('users.export')->middleware('can:view users');
    Route::post('/users/bulk',                       [UserController::class, 'bulk'])->name('users.bulk')->middleware('can:edit users');
    Route::get('/users/create',                      [UserController::class, 'create'])->name('users.create')->middleware('can:create users');
    Route::post('/users',                            [UserController::class, 'store'])->name('users.store')->middleware('can:create users');
    Route::get('/users/{user}/edit',                 [UserController::class, 'edit'])->name('users.edit')->middleware('can:edit users');
    Route::put('/users/{user}',                      [UserController::class, 'update'])->name('users.update')->middleware('can:edit users');
    Route::patch('/users/{user}/suspend',            [UserController::class, 'suspend'])->name('users.suspend')->middleware('can:suspend users');
    Route::patch('/users/{user}/activate',           [UserController::class, 'activate'])->name('users.activate')->middleware('can:suspend users');
    Route::patch('/users/{id}/restore',              [UserController::class, 'restore'])->name('users.restore')->middleware('can:delete users');
    Route::delete('/users/{user}',                   [UserController::class, 'destroy'])->name('users.destroy')->middleware('can:delete users');

    // Roles — admin only
    Route::get('/roles',              [RoleController::class, 'index'])->name('roles.index')->middleware('role:admin');
    Route::post('/roles',             [RoleController::class, 'store'])->name('roles.store')->middleware('role:admin');
    Route::put('/roles/permissions',  [RoleController::class, 'syncPermissions'])->name('roles.permissions')->middleware('role:admin');
    Route::put('/roles/{role}',       [RoleController::class, 'update'])->name('roles.update')->middleware('role:admin');
    Route::delete('/roles/{role}',    [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('role:admin');

    Route::get('/settings',              [SettingsController::class, 'index'])->name('settings')->middleware('can:view settings');
    Route::put('/settings',              [SettingsController::class, 'update'])->name('settings.update')->middleware('can:edit settings');
    Route::post('/settings/smtp-test',   [SettingsController::class, 'smtpTest'])->name('settings.smtp-test')->middleware('can:edit settings');
    Route::get('/activity',         [ActivityController::class, 'index'])->name('activity')->middleware('can:view activity log');
    Route::get('/activity/export',  [ActivityController::class, 'export'])->name('activity.export')->middleware('can:view activity log');
    Route::post('/cache/clear', [DashboardController::class, 'clearCache'])->name('cache.clear')->middleware('can:edit settings');

    Route::get('/languages',                        [LanguageController::class, 'index'])->name('languages.index');
    Route::post('/languages',                       [LanguageController::class, 'store'])->name('languages.store');
    Route::delete('/languages/{language}',          [LanguageController::class, 'destroy'])->name('languages.destroy');
    Route::patch('/languages/{language}/default',   [LanguageController::class, 'setDefault'])->name('languages.default');
    Route::put('/languages/{language}/lines',       [LanguageController::class, 'updateLine'])->name('languages.lines');
    Route::get('/languages/{language}/edit',        [LanguageController::class, 'edit'])->name('languages.edit');
    Route::put('/languages/{language}/batch',       [LanguageController::class, 'batch'])->name('languages.batch');
    Route::post('/languages/{language}/reseed',     [LanguageController::class, 'reseed'])->name('languages.reseed');
    Route::get('/languages/{language}/export',      [LanguageController::class, 'export'])->name('languages.export');
    Route::post('/languages/{language}/import',     [LanguageController::class, 'import'])->name('languages.import');
    Route::get('/languages/{language}/meta',        [LanguageController::class, 'meta'])->name('languages.meta');
    Route::patch('/languages/{language}/meta',      [LanguageController::class, 'updateMeta'])->name('languages.meta.update');

    Route::resource('pages', \App\Http\Controllers\Admin\PagesController::class);

    Route::get('/search',     [GlobalSearchController::class, 'search'])->name('search');

    // Self-updater — admin only
    Route::get('/updates',         [UpdateController::class, 'index'])->name('updates')->middleware('role:admin');
    Route::get('/updates/check',   [UpdateController::class, 'check'])->name('updates.check')->middleware('role:admin');
    Route::post('/updates/install',[UpdateController::class, 'install'])->name('updates.install')->middleware('role:admin');

    Route::get('/license',    [LicenseController::class, 'show'])->name('license');
    Route::post('/license',   [LicenseController::class, 'update'])->name('license.update');
    Route::delete('/license', [LicenseController::class, 'remove'])->name('license.remove');

    // UI Showcase (TailAdmin elements)
    Route::prefix('ui')->name('ui.')->group(function () {
        $pages = [
            'ecommerce', 'form-elements', 'tables', 'alerts', 'avatars', 'badges',
            'buttons', 'images', 'videos', 'calendar', 'bar-chart',
            'line-chart', 'blank',
        ];
        foreach ($pages as $page) {
            Route::get('/'.$page, fn () => view('admin.showcase.'.$page))->name($page);
        }

        // User Profile (wired to real user)
        Route::get('/profile',  [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    });
});
