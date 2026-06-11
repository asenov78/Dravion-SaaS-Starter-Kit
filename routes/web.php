<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// Installer — disabled after install.lock exists
Route::middleware(\App\Http\Middleware\InstallGuard::class)
    ->prefix('install')->name('install.')->group(function () {
        Route::get('/', [InstallController::class, 'index'])->name('index');
        Route::get('/{step}', [InstallController::class, 'show'])->name('step');
        Route::post('/{step}', [InstallController::class, 'process'])->name('process');
    });

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// User dashboard
Route::middleware('auth')->get('/dashboard', fn () => view('dashboard'))->name('dashboard');

// Admin
Route::middleware(['auth', 'role:admin|manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::patch('/users/{user}/suspend',  [UserController::class, 'suspend'])->name('users.suspend');
    Route::patch('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');

    Route::get('/settings',  [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings',  [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/activity',  [ActivityController::class, 'index'])->name('activity');
});
