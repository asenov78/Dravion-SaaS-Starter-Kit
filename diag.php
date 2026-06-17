<?php
// Storage diagnostic — delete this file after debugging
// Access: https://yourdomain.com/dravion/diag.php

// ── Raw PHP section (no Laravel) ──────────────────────────────────────────────
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

$scheme      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host        = $_SERVER['HTTP_HOST'] ?? '?';
$scriptDir   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$detectedUrl = $scheme . '://' . $host . $scriptDir;

$configCache    = __DIR__ . '/bootstrap/cache/config.php';
$storageBase    = __DIR__ . '/storage/app/public';
$avatarDir      = $storageBase . '/avatars';
$symlink        = __DIR__ . '/public/storage';
$symlinkTarget  = is_link($symlink) ? readlink($symlink) : null;
$symlinkWorks   = is_link($symlink) && is_dir($symlink);

$testFile    = null;
$testRelPath = null;
foreach (array_merge(glob($avatarDir . '/*.jpg') ?: [], glob($avatarDir . '/*.jpeg') ?: [], glob($avatarDir . '/*.png') ?: []) as $f) {
    $testFile    = $f;
    $testRelPath = 'avatars/' . basename($f);
    break;
}

$configCachedUrl = null;
if (file_exists($configCache)) {
    $cached = file_get_contents($configCache);
    if (preg_match("/'url'\s*=>\s*'([^']+\/storage)'/", $cached, $m)) {
        $configCachedUrl = $m[1];
    }
}

// ── Laravel bootstrap section ─────────────────────────────────────────────────
$laravelError   = null;
$laravelAppUrl  = null;
$storageUrlFromLaravel = null;
$storageTestUrl = null;
$dbUsers        = [];
$logoValue      = null;

try {
    define('LARAVEL_START', microtime(true));
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    // Boot without handling request — just boot the app
    $app->boot();

    $laravelAppUrl  = config('app.url');
    $storageUrlFromLaravel = config('filesystems.disks.public.url');
    $storageTestUrl = \Illuminate\Support\Facades\Storage::disk('public')->url('avatars/test.jpg');

    // First 3 users with avatars
    $users = \App\Models\User::whereNotNull('avatar')->where('avatar', '!=', '')->limit(3)->get(['id','name','email','avatar']);
    foreach ($users as $u) {
        $dbUsers[] = [
            'id'        => $u->id,
            'name'      => $u->name,
            'avatar_db' => $u->avatar,
            'url'       => \Illuminate\Support\Facades\Storage::disk('public')->url($u->avatar),
        ];
    }

    // Logo setting
    try {
        $logoValue = \App\Models\Setting::get('logo');
    } catch (\Throwable) {}

} catch (\Throwable $e) {
    $laravelError = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
}

function ok($v)  { return $v ? '✅' : '❌'; }
function val($v) { return '<code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;word-break:break-all">' . htmlspecialchars((string)$v) . '</code>'; }
function match_ok($a, $b) { return $a === $b ? '✅ match' : '❌ MISMATCH'; }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Storage Diagnostic</title>
<style>
body{font-family:system-ui,sans-serif;max-width:900px;margin:40px auto;padding:0 20px;color:#1f2937;line-height:1.6}
h1{font-size:1.4rem;font-weight:700;margin-bottom:4px}
.sub{color:#6b7280;font-size:.9rem;margin-bottom:28px}
table{width:100%;border-collapse:collapse;margin-bottom:24px}
th{text-align:left;padding:8px 12px;background:#f9fafb;border-bottom:2px solid #e5e7eb;font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:#6b7280}
td{padding:8px 12px;border-bottom:1px solid #f3f4f6;font-size:.88rem;vertical-align:top}
tr:last-child td{border-bottom:none}
.section{margin:24px 0 6px;font-weight:700;color:#111827;font-size:1rem;border-left:3px solid #6366f1;padding-left:10px}
code{background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:.83rem;word-break:break-all}
.warn{background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:.9rem}
.err{background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;margin-bottom:16px;font-size:.85rem;color:#991b1b}
.img-row{display:flex;gap:12px;flex-wrap:wrap;margin-top:8px}
.img-box{border:2px dashed #d1d5db;border-radius:8px;padding:10px;text-align:center;max-width:200px}
.img-box img{max-height:70px;max-width:180px;border-radius:6px}
.img-box p{margin:4px 0 0;font-size:.75rem;color:#9ca3af;word-break:break-all}
</style>
</head>
<body>
<h1>🔍 Storage Diagnostic v2</h1>
<p class="sub">Delete <code>diag.php</code> after debugging.</p>
<div class="warn">⚠️ Exposes server paths + DB data. Remove immediately after use.</div>

<div class="section">1. Raw PHP — APP_URL</div>
<table>
<tr><th>Key</th><th>Value</th><th>Status</th></tr>
<tr><td>APP_URL in .env</td><td><?= val($appUrl) ?></td><td><?= ok($appUrl !== '(not set)') ?></td></tr>
<tr><td>Detected from SCRIPT_NAME</td><td><?= val($detectedUrl) ?></td><td><?= match_ok(rtrim($appUrl,'/'), rtrim($detectedUrl,'/')) ?></td></tr>
<tr><td>Storage URL (raw)</td><td><?= val($storageUrl) ?></td><td></td></tr>
<tr><td>Config cache</td><td><?= val(file_exists($configCache) ? 'EXISTS' : 'No cache') ?></td><td><?= ok(!file_exists($configCache)) ?></td></tr>
<?php if ($configCachedUrl): ?>
<tr><td>Cached storage URL</td><td><?= val($configCachedUrl) ?></td><td><?= match_ok($configCachedUrl, $storageUrl) ?></td></tr>
<?php endif; ?>
<tr><td>SCRIPT_NAME</td><td><?= val($_SERVER['SCRIPT_NAME'] ?? '?') ?></td><td></td></tr>
</table>

<div class="section">2. Laravel Bootstrap</div>
<?php if ($laravelError): ?>
<div class="err">❌ Laravel failed to boot: <?= htmlspecialchars($laravelError) ?></div>
<?php else: ?>
<table>
<tr><th>Key</th><th>Value</th><th>Status</th></tr>
<tr><td>config('app.url')</td><td><?= val($laravelAppUrl) ?></td><td><?= match_ok($laravelAppUrl, rtrim($appUrl,'/')) ?></td></tr>
<tr><td>config('filesystems.disks.public.url')</td><td><?= val($storageUrlFromLaravel) ?></td><td><?= match_ok($storageUrlFromLaravel, $storageUrl) ?></td></tr>
<tr><td>Storage::disk('public')->url('avatars/test.jpg')</td><td><?= val($storageTestUrl) ?></td><td><?= ok(str_contains((string)$storageTestUrl, '/dravion/storage/')) ?></td></tr>
</table>
<?php endif; ?>

<div class="section">3. DB Users — avatar column + generated URL</div>
<?php if (empty($dbUsers)): ?>
<p style="color:#6b7280;font-size:.9rem">No users with avatars found in DB.</p>
<?php else: ?>
<table>
<tr><th>User</th><th>avatar (DB value)</th><th>Storage::url() output</th><th>Correct?</th></tr>
<?php foreach ($dbUsers as $u): ?>
<tr>
  <td><?= val($u['name']) ?><br><span style="color:#9ca3af;font-size:.8rem">#<?= $u['id'] ?></span></td>
  <td><?= val($u['avatar_db']) ?></td>
  <td><?= val($u['url']) ?></td>
  <td><?= ok(str_contains($u['url'], '/dravion/storage/')) ?></td>
</tr>
<?php endforeach; ?>
</table>

<div class="img-row">
<?php foreach ($dbUsers as $u): ?>
<div class="img-box">
  <img src="<?= htmlspecialchars($u['url']) ?>" alt="<?= htmlspecialchars($u['name']) ?>"
       onerror="this.style.outline='2px solid red';this.alt='FAILED'">
  <p><?= htmlspecialchars($u['name']) ?></p>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($logoValue): ?>
<div class="section">4. Logo Setting</div>
<table>
<tr><th>DB value</th><th>Storage::url() output</th></tr>
<tr>
  <td><?= val($logoValue) ?></td>
  <td><?= val(!$laravelError ? \Illuminate\Support\Facades\Storage::disk('public')->url($logoValue) : 'N/A') ?></td>
</tr>
</table>
<?php if (!$laravelError): ?>
<div class="img-row">
  <div class="img-box">
    <img src="<?= htmlspecialchars(\Illuminate\Support\Facades\Storage::disk('public')->url($logoValue)) ?>" alt="logo"
         onerror="this.style.outline='2px solid red';this.alt='FAILED'">
    <p>Logo</p>
  </div>
</div>
<?php endif; ?>
<?php endif; ?>

<div class="section">5. Symlink + Filesystem</div>
<table>
<tr><th>Key</th><th>Value</th><th>Status</th></tr>
<tr><td>public/storage symlink</td><td><?= val(is_link($symlink) ? 'YES' : (file_exists($symlink) ? 'dir' : 'NO')) ?></td><td><?= ok(is_link($symlink)) ?></td></tr>
<tr><td>Symlink target</td><td><?= val($symlinkTarget ?? 'n/a') ?></td><td></td></tr>
<tr><td>Symlink resolves</td><td><?= val($symlinkWorks ? 'YES' : 'NO') ?></td><td><?= ok($symlinkWorks) ?></td></tr>
<tr><td>storage/app/public/</td><td><?= val($storageBase) ?></td><td><?= ok(is_dir($storageBase)) ?></td></tr>
<tr><td>avatars/</td><td><?= val($avatarDir) ?></td><td><?= ok(is_dir($avatarDir)) ?></td></tr>
<tr><td>Test file</td><td><?= val($testFile ?? 'none') ?></td><td><?= ok($testFile !== null) ?></td></tr>
</table>

<?php if ($testFile): ?>
<div class="section">6. Direct URL Test</div>
<p style="font-size:.88rem;color:#6b7280;margin-bottom:8px">URL: <?= val($storageUrl . '/' . $testRelPath) ?></p>
<div class="img-row">
  <div class="img-box">
    <img src="<?= htmlspecialchars($storageUrl . '/' . $testRelPath) ?>" alt="test"
         onerror="this.style.outline='2px solid red';this.alt='FAILED'">
    <p>Direct URL test</p>
  </div>
</div>
<?php endif; ?>

<p style="margin-top:32px;font-size:.8rem;color:#9ca3af">
  PHP <?= PHP_VERSION ?> | SAPI: <?= php_sapi_name() ?> | <?= date('Y-m-d H:i:s') ?>
</p>
</body>
</html>
