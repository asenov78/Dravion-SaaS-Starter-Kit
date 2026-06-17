<?php
// Storage diagnostic v3 — delete this file after debugging
// Access: https://apsbg.com/dravion/diag.php

// ── Raw PHP (no Laravel) ──────────────────────────────────────────────────────
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

$scheme     = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host       = $_SERVER['HTTP_HOST'] ?? '?';
$scriptDir  = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$detected   = $scheme . '://' . $host . $scriptDir;

$configCache = __DIR__ . '/bootstrap/cache/config.php';
$storageBase = __DIR__ . '/storage/app/public';
$avatarDir   = $storageBase . '/avatars';
$symlink     = __DIR__ . '/public/storage';
$symlinkTarget = is_link($symlink) ? readlink($symlink) : null;
$symlinkWorks  = is_link($symlink) && is_dir($symlink);

$testFile = $testRel = null;
foreach (array_merge(glob($avatarDir . '/*.jpg') ?: [], glob($avatarDir . '/*.png') ?: []) as $f) {
    $testFile = $f; $testRel = 'avatars/' . basename($f); break;
}

$cachedStorageUrl = null;
if (file_exists($configCache)) {
    $c = file_get_contents($configCache);
    if (preg_match("/'url'\s*=>\s*'([^']+\/storage)'/", $c, $m)) $cachedStorageUrl = $m[1];
}

// ── Laravel bootstrap ─────────────────────────────────────────────────────────
$laravelError = null;
$lConfig  = $lDiskUrl = $lStorageUrl = null;
$dbUsers  = [];
$logoVal  = null;
$logoUrl  = null;

try {
    if (!defined('LARAVEL_START')) define('LARAVEL_START', microtime(true));

    require __DIR__ . '/vendor/autoload.php';
    $app = require __DIR__ . '/bootstrap/app.php';

    // Simulate a request so providers boot properly
    $_SERVER['REQUEST_URI']    = '/diag-boot';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $kernel   = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $request  = \Illuminate\Http\Request::create('/diag-boot', 'GET');
    $app->instance('request', $request);

    // Boot the application
    $app->boot();

    $lConfig     = config('app.url');
    $lDiskUrl    = config('filesystems.disks.public.url');
    $lStorageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url('avatars/sample.jpg');

    // DB: first 3 users with non-null avatar
    try {
        $rows = \App\Models\User::whereNotNull('avatar')->where('avatar', '!=', '')->limit(3)->get(['id','name','avatar']);
        foreach ($rows as $u) {
            $dbUsers[] = [
                'id'     => $u->id,
                'name'   => $u->name,
                'db'     => $u->avatar,
                'url'    => \Illuminate\Support\Facades\Storage::disk('public')->url($u->avatar),
            ];
        }
    } catch (\Throwable $ex) {
        $dbUsers = [['error' => $ex->getMessage()]];
    }

    // Logo setting
    try {
        $logoVal = \App\Models\Setting::get('logo');
        if ($logoVal) $logoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($logoVal);
    } catch (\Throwable) {}

} catch (\Throwable $e) {
    $laravelError = $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine();
}

function ok($v)  { return $v ? '✅' : '❌'; }
function val($v, $class='') { return '<code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;word-break:break-all;' . $class . '">' . htmlspecialchars((string)$v) . '</code>'; }
function match_badge($a, $b) {
    $ok = trim((string)$a) === trim((string)$b);
    $icon = $ok ? '✅' : '❌';
    $label = $ok ? 'match' : 'MISMATCH';
    return "$icon $label";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Diag v3</title>
<style>
*{box-sizing:border-box}
body{font-family:system-ui,sans-serif;max-width:960px;margin:40px auto;padding:0 20px;color:#111;line-height:1.55}
h1{font-size:1.35rem;font-weight:700;margin-bottom:4px}
.sub{color:#6b7280;font-size:.88rem;margin-bottom:24px}
table{width:100%;border-collapse:collapse;margin-bottom:20px;font-size:.85rem}
th{text-align:left;padding:7px 11px;background:#f9fafb;border-bottom:2px solid #e5e7eb;font-size:.76rem;text-transform:uppercase;letter-spacing:.04em;color:#6b7280}
td{padding:7px 11px;border-bottom:1px solid #f3f4f6;vertical-align:top}
.sec{margin:22px 0 5px;font-weight:700;font-size:.95rem;border-left:3px solid #6366f1;padding-left:10px}
code{background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:.82rem;word-break:break-all}
.warn{background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;padding:9px 13px;margin-bottom:14px;font-size:.88rem}
.err{background:#fef2f2;border:1px solid #fca5a5;border-radius:8px;padding:9px 13px;margin-bottom:14px;font-size:.83rem;color:#991b1b;white-space:pre-wrap}
.imgs{display:flex;gap:10px;flex-wrap:wrap;margin-top:8px}
.ibox{border:2px dashed #d1d5db;border-radius:8px;padding:8px;text-align:center;max-width:170px}
.ibox img{max-height:60px;max-width:150px;border-radius:5px;display:block;margin:0 auto}
.ibox p{margin:3px 0 0;font-size:.72rem;color:#9ca3af;word-break:break-all}
.hi{background:#fef9c3}
</style>
</head>
<body>
<h1>🔍 Storage Diagnostic v3</h1>
<p class="sub">Remove <code>diag.php</code> after debugging.</p>
<div class="warn">⚠️ Exposes server info + DB data. Delete immediately after use.</div>

<div class="sec">1. Raw PHP — APP_URL / .env</div>
<table>
<tr><th>Key</th><th>Value</th><th></th></tr>
<tr><td>APP_URL in .env</td><td><?= val($appUrl) ?></td><td><?= ok($appUrl !== '(not set)') ?></td></tr>
<tr><td>Detected from SCRIPT_NAME</td><td><?= val($detected) ?></td><td><?= match_badge(rtrim($appUrl,'/'), rtrim($detected,'/')) ?></td></tr>
<tr><td>Expected storage URL</td><td><?= val($storageUrl) ?></td><td></td></tr>
<tr><td>Config cache</td><td><?= val(file_exists($configCache) ? 'EXISTS ⚠️' : 'No cache') ?></td><td><?= ok(!file_exists($configCache)) ?></td></tr>
<?php if ($cachedStorageUrl): ?>
<tr><td style="background:#fef2f2">Cached storage URL</td><td style="background:#fef2f2"><?= val($cachedStorageUrl) ?></td><td><?= match_badge($cachedStorageUrl, $storageUrl) ?></td></tr>
<?php endif; ?>
<tr><td>SCRIPT_NAME</td><td><?= val($_SERVER['SCRIPT_NAME'] ?? '?') ?></td><td></td></tr>
</table>

<div class="sec">2. Laravel — config + Storage::url()</div>
<?php if ($laravelError): ?>
<div class="err">❌ Laravel boot failed:
<?= htmlspecialchars($laravelError) ?></div>
<?php else: ?>
<table>
<tr><th>Key</th><th>Value</th><th></th></tr>
<tr><td>config('app.url')</td><td class="<?= trim($lConfig) !== trim($appUrl) ? 'hi' : '' ?>"><?= val($lConfig) ?></td><td><?= match_badge($lConfig, rtrim($appUrl,'/')) ?></td></tr>
<tr><td>config('filesystems.disks.public.url')</td><td class="<?= trim($lDiskUrl) !== trim($storageUrl) ? 'hi' : '' ?>"><?= val($lDiskUrl) ?></td><td><?= match_badge($lDiskUrl, $storageUrl) ?></td></tr>
<tr><td>Storage::disk('public')->url('avatars/sample.jpg')</td><td><?= val($lStorageUrl) ?></td><td><?= ok(str_contains((string)$lStorageUrl, '/dravion/storage/')) ?></td></tr>
</table>
<?php endif; ?>

<div class="sec">3. DB Users — avatar column → generated URL</div>
<?php if (empty($dbUsers)): ?>
<p style="color:#6b7280;font-size:.88rem">No users with avatar in DB.</p>
<?php else: ?>
<table>
<tr><th>User</th><th>DB value</th><th>Storage::url() →</th><th>Has /dravion/?</th></tr>
<?php foreach ($dbUsers as $u): ?>
<?php if (isset($u['error'])): ?>
<tr><td colspan="4" style="color:red"><?= htmlspecialchars($u['error']) ?></td></tr>
<?php else: ?>
<tr>
  <td><?= htmlspecialchars($u['name']) ?> <span style="color:#9ca3af;font-size:.8rem">#<?= $u['id'] ?></span></td>
  <td><?= val($u['db']) ?></td>
  <td class="<?= !str_contains($u['url'], '/dravion/') ? 'hi' : '' ?>"><?= val($u['url']) ?></td>
  <td><?= ok(str_contains($u['url'], '/dravion/storage/')) ?></td>
</tr>
<?php endif; ?>
<?php endforeach; ?>
</table>
<div class="imgs">
<?php foreach ($dbUsers as $u): if (isset($u['error'])) continue; ?>
<div class="ibox">
  <img src="<?= htmlspecialchars($u['url'] ?? '') ?>" alt="<?= htmlspecialchars($u['name'] ?? '') ?>"
       onerror="this.style.border='2px solid red';this.alt='FAILED'">
  <p><?= htmlspecialchars($u['name'] ?? '') ?></p>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php if ($logoVal): ?>
<div class="sec">4. Logo Setting</div>
<table>
<tr><th>DB value</th><th>Storage::url() →</th><th>Has /dravion/?</th></tr>
<tr>
  <td><?= val($logoVal) ?></td>
  <td class="<?= $logoUrl && !str_contains($logoUrl, '/dravion/') ? 'hi' : '' ?>"><?= val($logoUrl ?? '(error)') ?></td>
  <td><?= ok($logoUrl && str_contains($logoUrl, '/dravion/storage/')) ?></td>
</tr>
</table>
<?php if ($logoUrl): ?>
<div class="imgs">
  <div class="ibox">
    <img src="<?= htmlspecialchars($logoUrl) ?>" alt="logo" onerror="this.style.border='2px solid red';this.alt='FAILED'">
    <p>Logo</p>
  </div>
</div>
<?php endif; ?>
<?php endif; ?>

<div class="sec">5. Symlink + Filesystem</div>
<table>
<tr><th>Key</th><th>Value</th><th></th></tr>
<tr><td>public/storage symlink</td><td><?= val(is_link($symlink) ? 'is_link=YES' : (file_exists($symlink) ? 'directory (not symlink)' : 'MISSING')) ?></td><td><?= ok(is_link($symlink)) ?></td></tr>
<tr><td>Symlink target</td><td><?= val($symlinkTarget ?? 'n/a') ?></td><td></td></tr>
<tr><td>Symlink resolves (is_dir)</td><td><?= val($symlinkWorks ? 'YES' : 'NO') ?></td><td><?= ok($symlinkWorks) ?></td></tr>
<tr><td>storage/app/public/</td><td><?= val($storageBase) ?></td><td><?= ok(is_dir($storageBase)) ?></td></tr>
<tr><td>avatars/ dir</td><td><?= val($avatarDir) ?></td><td><?= ok(is_dir($avatarDir)) ?></td></tr>
<tr><td>Test file</td><td><?= val($testFile ?? 'none found') ?></td><td><?= ok($testFile !== null) ?></td></tr>
</table>

<?php if ($testFile): ?>
<div class="sec">6. Direct URL Image Test</div>
<p style="font-size:.85rem;color:#6b7280;margin-bottom:6px">URL: <?= val($storageUrl . '/' . $testRel) ?></p>
<div class="imgs">
  <div class="ibox">
    <img src="<?= htmlspecialchars($storageUrl . '/' . $testRel) ?>" alt="direct-test"
         onerror="this.style.border='2px solid red';this.alt='FAILED'">
    <p>Direct URL (.env based)</p>
  </div>
  <?php if (!$laravelError && $lStorageUrl): ?>
  <div class="ibox">
    <img src="<?= htmlspecialchars(str_replace('avatars/sample.jpg', $testRel, $lStorageUrl)) ?>" alt="laravel-test"
         onerror="this.style.border='2px solid red';this.alt='FAILED'">
    <p>Laravel Storage::url()</p>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<p style="margin-top:28px;font-size:.78rem;color:#9ca3af">
  PHP <?= PHP_VERSION ?> | SAPI: <?= php_sapi_name() ?> | <?= date('Y-m-d H:i:s') ?>
</p>
</body>
</html>
