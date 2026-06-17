<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Show PHP errors before install is complete (helps diagnose 500s on shared hosting)
if (! file_exists(__DIR__ . '/storage/install.lock')) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Shared hosting entry point — serves Laravel from project root
// without requiring document root to point to /public

// Friendly error if vendor/ is missing (common after incomplete upload)
if (! file_exists(__DIR__ . '/vendor/autoload.php')) {
    http_response_code(503);
    echo '<h2>Installation incomplete</h2>';
    echo '<p>The <strong>vendor/</strong> directory is missing.</p>';
    echo '<p>Please upload the full installation ZIP (not the source ZIP from GitHub).</p>';
    echo '<p>The correct file is attached to the GitHub Release and already contains all dependencies.</p>';
    exit;
}

// Bootstrap .env for first-time install
if (! file_exists(__DIR__ . '/.env')) {
    if (file_exists(__DIR__ . '/.env.installer')) {
        copy(__DIR__ . '/.env.installer', __DIR__ . '/.env');
    } else {
        // No .env.installer either — generate a minimal .env from scratch
        $key = 'base64:' . base64_encode(openssl_random_pseudo_bytes(32));
        $minimal = "APP_NAME=Dravion\nAPP_ENV=production\nAPP_KEY={$key}\nAPP_DEBUG=false\n"
                 . "APP_URL=http://localhost\nSESSION_DRIVER=file\n"
                 . "DB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\n"
                 . "DB_DATABASE=\nDB_USERNAME=\nDB_PASSWORD=\n";
        file_put_contents(__DIR__ . '/.env', $minimal);
        putenv('APP_KEY=' . $key);
        $_ENV['APP_KEY'] = $key;
        $_SERVER['APP_KEY'] = $key;
    }
}

// Auto-detect APP_URL — fix placeholder AND missing subdirectory (e.g. APP_URL=https://domain.com when app lives at /dravion/)
if (file_exists(__DIR__ . '/.env')) {
    $envContent  = file_get_contents(__DIR__ . '/.env');
    $scheme      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host        = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');
    $detectedUrl = $scheme . '://' . $host . $scriptDir;

    $currentUrl  = '';
    if (preg_match('/^APP_URL=(.+)$/m', $envContent, $m)) {
        $currentUrl = rtrim(trim($m[1]), '/');
    }

    // Update if placeholder, or if subdirectory is missing from current URL
    if ($currentUrl === 'http://localhost' || ($scriptDir !== '' && $currentUrl !== rtrim($detectedUrl, '/'))) {
        if (preg_match('/^APP_URL=.*$/m', $envContent)) {
            $envContent = preg_replace('/^APP_URL=.*$/m', 'APP_URL=' . $detectedUrl, $envContent);
        } else {
            $envContent .= "\nAPP_URL=" . $detectedUrl;
        }
        file_put_contents(__DIR__ . '/.env', $envContent);
        putenv('APP_URL=' . $detectedUrl);
        $_ENV['APP_URL']    = $detectedUrl;
        $_SERVER['APP_URL'] = $detectedUrl;
    }
}

// Ensure APP_KEY is set — generate and persist a real key if missing or empty
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    if (! preg_match('/^APP_KEY=.+$/m', $envContent)) {
        $key = 'base64:' . base64_encode(openssl_random_pseudo_bytes(32));
        if (preg_match('/^APP_KEY=.*$/m', $envContent)) {
            $envContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $envContent);
        } else {
            $envContent .= "\nAPP_KEY=" . $key;
        }
        file_put_contents(__DIR__ . '/.env', $envContent);
        putenv('APP_KEY=' . $key);
        $_ENV['APP_KEY'] = $key;
        $_SERVER['APP_KEY'] = $key;
    }
}

// Ensure required runtime directories exist (ZIP extraction skips empty dirs)
foreach ([
    __DIR__ . '/bootstrap/cache',
    __DIR__ . '/storage/logs',
    __DIR__ . '/storage/framework/cache/data',
    __DIR__ . '/storage/framework/sessions',
    __DIR__ . '/storage/framework/views',
] as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

if (file_exists($maintenance = __DIR__ . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
