<?php
// Storage diagnostic — delete this file after debugging
// Access: https://yourdomain.com/dravion/diag.php

// Parse .env manually (works before Laravel boots)
$env = [];
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v, " \t\n\r\"'");
    }
}

$appUrl     = $env['APP_URL'] ?? '(not set)';
$storageUrl = rtrim($appUrl, '/') . '/storage';

// Detect request info
$scheme    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? '?';
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$detectedUrl = $scheme . '://' . $host . $scriptDir;

// Find a real avatar file to test with
$storageBase = __DIR__ . '/storage/app/public';
$avatarDir   = $storageBase . '/avatars';
$testFile    = null;
$testRelPath = null;
if (is_dir($avatarDir)) {
    foreach (glob($avatarDir . '/*.jpg') ?: glob($avatarDir . '/*.jpeg') ?: glob($avatarDir . '/*.png') ?: [] as $f) {
        $testFile    = $f;
        $testRelPath = 'avatars/' . basename($f);
        break;
    }
}

// Config cache
$configCache = __DIR__ . '/bootstrap/cache/config.php';
$configCachedUrl = null;
if (file_exists($configCache)) {
    $cached = file_get_contents($configCache);
    if (preg_match("/'url'\s*=>\s*'([^']+\/storage)'/", $cached, $m)) {
        $configCachedUrl = $m[1];
    }
}

// Symlink
$symlink     = __DIR__ . '/public/storage';
$symlinkTarget = is_link($symlink) ? readlink($symlink) : null;
$symlinkWorks  = is_link($symlink) && is_dir($symlink);

// Test: can PHP serve the file via the route path?
$phpRouteUrl = $storageUrl . '/' . $testRelPath;

function ok($v)  { return $v ? '✅' : '❌'; }
function val($v) { return '<code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;">' . htmlspecialchars((string)$v) . '</code>'; }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Storage Diagnostic</title>
<style>
body{font-family:system-ui,sans-serif;max-width:860px;margin:40px auto;padding:0 20px;color:#1f2937;line-height:1.6}
h1{font-size:1.4rem;font-weight:700;margin-bottom:4px}
.sub{color:#6b7280;font-size:.9rem;margin-bottom:28px}
table{width:100%;border-collapse:collapse;margin-bottom:24px}
th{text-align:left;padding:8px 12px;background:#f9fafb;border-bottom:2px solid #e5e7eb;font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:#6b7280}
td{padding:8px 12px;border-bottom:1px solid #f3f4f6;font-size:.9rem;vertical-align:top}
tr:last-child td{border-bottom:none}
.section{margin-bottom:8px;font-weight:600;color:#111827;font-size:1rem}
code{background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:.85rem}
.warn{background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:.9rem}
.img-test{margin-top:16px;border:2px dashed #d1d5db;border-radius:8px;padding:20px;text-align:center}
.img-test img{max-height:80px;max-width:200px;border-radius:6px}
</style>
</head>
<body>
<h1>🔍 Storage Diagnostic</h1>
<p class="sub">Delete <code>diag.php</code> from server after debugging.</p>

<div class="warn">⚠️ This file exposes server paths. Remove it once the issue is fixed.</div>

<p class="section">1. APP_URL</p>
<table>
<tr><th>Key</th><th>Value</th><th>Status</th></tr>
<tr>
  <td>APP_URL in .env</td>
  <td><?= val($appUrl) ?></td>
  <td><?= ok($appUrl !== '(not set)') ?></td>
</tr>
<tr>
  <td>Detected from SCRIPT_NAME</td>
  <td><?= val($detectedUrl) ?></td>
  <td><?= ok($detectedUrl === rtrim($appUrl, '/')) ? '✅ matches .env' : '⚠️ MISMATCH — .env needs update' ?></td>
</tr>
<tr>
  <td>Storage URL (from .env)</td>
  <td><?= val($storageUrl) ?></td>
  <td></td>
</tr>
<tr>
  <td>Config cache exists?</td>
  <td><?= val(file_exists($configCache) ? 'YES — ' . $configCache : 'No cache') ?></td>
  <td><?= ok(!file_exists($configCache)) ?> (no cache = good)</td>
</tr>
<?php if ($configCachedUrl): ?>
<tr>
  <td>Storage URL in config cache</td>
  <td><?= val($configCachedUrl) ?></td>
  <td><?= ok($configCachedUrl === $storageUrl) ? '✅ matches' : '❌ STALE — differs from .env' ?></td>
</tr>
<?php endif; ?>
<tr>
  <td>SCRIPT_NAME</td>
  <td><?= val($_SERVER['SCRIPT_NAME'] ?? '?') ?></td>
  <td></td>
</tr>
<tr>
  <td>REQUEST_URI</td>
  <td><?= val($_SERVER['REQUEST_URI'] ?? '?') ?></td>
  <td></td>
</tr>
</table>

<p class="section">2. Symlink</p>
<table>
<tr><th>Key</th><th>Value</th><th>Status</th></tr>
<tr>
  <td>public/storage symlink exists?</td>
  <td><?= val(is_link($symlink) ? 'YES (link)' : (file_exists($symlink) ? 'YES (dir)' : 'NO')) ?></td>
  <td><?= ok(is_link($symlink)) ?></td>
</tr>
<tr>
  <td>Symlink target</td>
  <td><?= val($symlinkTarget ?? 'n/a') ?></td>
  <td></td>
</tr>
<tr>
  <td>Symlink works (is dir)?</td>
  <td><?= val($symlinkWorks ? 'YES' : 'NO') ?></td>
  <td><?= ok($symlinkWorks) ?></td>
</tr>
</table>

<p class="section">3. Storage Filesystem</p>
<table>
<tr><th>Key</th><th>Value</th><th>Status</th></tr>
<tr>
  <td>storage/app/public/ exists?</td>
  <td><?= val($storageBase) ?></td>
  <td><?= ok(is_dir($storageBase)) ?></td>
</tr>
<tr>
  <td>avatars/ directory exists?</td>
  <td><?= val($avatarDir) ?></td>
  <td><?= ok(is_dir($avatarDir)) ?></td>
</tr>
<tr>
  <td>Test file found</td>
  <td><?= val($testFile ?? 'No avatar files found') ?></td>
  <td><?= ok($testFile !== null) ?></td>
</tr>
<?php if ($testFile): ?>
<tr>
  <td>File readable?</td>
  <td><?= val($testRelPath) ?></td>
  <td><?= ok(is_readable($testFile)) ?></td>
</tr>
<?php endif; ?>
</table>

<?php if ($testFile): ?>
<p class="section">4. URL Test</p>
<table>
<tr><th>URL that should load the image</th><th></th></tr>
<tr>
  <td><?= val($phpRouteUrl) ?></td>
  <td><a href="<?= htmlspecialchars($phpRouteUrl) ?>" target="_blank">Open →</a></td>
</tr>
<?php if ($symlinkWorks): ?>
<tr>
  <td><?= val($storageUrl . '/' . $testRelPath . ' (via symlink)') ?></td>
  <td><a href="<?= htmlspecialchars($storageUrl . '/' . $testRelPath) ?>" target="_blank">Open →</a></td>
</tr>
<?php endif; ?>
</table>

<div class="img-test">
  <p style="margin:0 0 12px;font-size:.9rem;color:#6b7280">Image rendered via PHP-generated URL:</p>
  <img src="<?= htmlspecialchars($phpRouteUrl) ?>" alt="test avatar"
       onerror="this.style.border='2px solid red';this.alt='FAILED TO LOAD'">
  <p style="margin:8px 0 0;font-size:.8rem;color:#9ca3af"><?= htmlspecialchars($phpRouteUrl) ?></p>
</div>
<?php endif; ?>

<p class="section">5. .htaccess Check</p>
<table>
<tr><th>File</th><th>Exists?</th></tr>
<tr><td>Root .htaccess</td><td><?= ok(file_exists(__DIR__ . '/.htaccess')) ?></td></tr>
<tr><td>public/.htaccess</td><td><?= ok(file_exists(__DIR__ . '/public/.htaccess')) ?></td></tr>
</table>

<p style="margin-top:32px;font-size:.8rem;color:#9ca3af">
  Generated: <?= date('Y-m-d H:i:s') ?> | PHP <?= PHP_VERSION ?> |
  SAPI: <?= php_sapi_name() ?>
</p>
</body>
</html>
