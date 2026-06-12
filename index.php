<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Shared hosting entry point — serves Laravel from project root
// without requiring document root to point to /public

// Bootstrap .env for first-time install: copy installer env if no .env exists
if (! file_exists(__DIR__.'/.env') && file_exists(__DIR__.'/.env.installer')) {
    copy(__DIR__.'/.env.installer', __DIR__.'/.env');
}

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
