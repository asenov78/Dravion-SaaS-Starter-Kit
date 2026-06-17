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

// Bootstrap .env for first-time install: copy installer env if no .env exists
if (! file_exists(__DIR__ . '/.env') && file_exists(__DIR__ . '/.env.installer')) {
    copy(__DIR__ . '/.env.installer', __DIR__ . '/.env');
}

if (file_exists($maintenance = __DIR__ . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__ . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__ . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
