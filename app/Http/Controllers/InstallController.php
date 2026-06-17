<?php

namespace App\Http\Controllers;

use App\Contracts\LicenseServiceInterface;
use App\Models\User;
use App\Services\EnvWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class InstallController extends Controller
{
    private array $steps = ['requirements', 'database', 'admin', 'license', 'finish'];

    public function __construct(private LicenseServiceInterface $license) {}

    public function index()
    {
        return redirect()->route('install.step', 'requirements');
    }

    public function show(string $step)
    {
        if (!in_array($step, $this->steps)) {
            abort(404);
        }

        $data = ['steps' => $this->steps, 'current' => $step];

        if ($step === 'requirements') {
            $data['checks'] = $this->runChecks();
        }

        if ($step === 'database') {
            if (request()->query('reset')) {
                session()->forget(['install_db', 'install_db_confirm_needed']);
            }
            $data['detected_url']   = $this->detectAppUrl();
            $data['confirm_needed'] = session('install_db_confirm_needed', 0);
            $data['saved_db']       = session('install_db', []);
        }

        return view("install.{$step}", $data);
    }

    public function process(Request $request, string $step)
    {
        return match ($step) {
            'requirements' => $this->handleRequirements($request),
            'database'     => $this->handleDatabase($request),
            'admin'        => $this->handleAdmin($request),
            'license'      => $this->handleLicense($request),
            'finish'       => $this->handleFinish($request),
            default        => abort(404),
        };
    }

    // ── Step handlers ──────────────────────────────────────────────────────

    private function handleRequirements(Request $request)
    {
        $checks = $this->runChecks();
        if (in_array(false, $checks, true)) {
            return back()->withErrors(['requirements' => 'Please fix the failing requirements before continuing.']);
        }

        // Auto-bootstrap .env so sessions use file driver before DB exists.
        $this->bootstrapEnv();

        return redirect()->route('install.step', 'database');
    }

    private function handleDatabase(Request $request)
    {
        // Phase 2: user confirmed drop — proceed without re-entering credentials
        if ($request->input('phase') === 'confirm') {
            $db = session('install_db');
            if (empty($db)) {
                return redirect()->route('install.step', 'database');
            }
            if ($request->input('confirm_drop') !== '1') {
                return back()->withErrors(['confirm_drop' => 'You must confirm before continuing.']);
            }
            // Mark confirmed
            session(['install_db' => array_merge($db, ['has_tables' => true])]);
            return redirect()->route('install.step', 'admin');
        }

        // Phase 1: validate & test connection
        $request->validate([
            'app_name'    => 'required|string|max:100',
            'app_url'     => 'required|url',
            'db_host'     => 'required|string',
            'db_port'     => 'required|integer',
            'db_name'     => 'required|string',
            'db_user'     => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            $pdo = new \PDO(
                "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_name};charset=utf8mb4",
                $request->db_user,
                $request->db_password ?? '',
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 5]
            );
        } catch (\PDOException $e) {
            return back()->withErrors(['db_host' => 'Connection failed: ' . $e->getMessage()])->withInput();
        }

        $dbData = array_merge(
            $request->only('app_name', 'app_url', 'db_host', 'db_port', 'db_name', 'db_user', 'db_password'),
            ['has_tables' => false]
        );

        // Check for existing tables
        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
        if (count($tables) > 0) {
            // Save credentials to session, redirect to confirmation phase
            session(['install_db' => $dbData, 'install_db_confirm_needed' => count($tables)]);
            return redirect()->route('install.step', 'database');
        }

        session(['install_db' => $dbData]);
        return redirect()->route('install.step', 'admin');
    }

    private function handleAdmin(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'password' => 'required|min:8|confirmed',
        ]);

        session(['install_admin' => $request->only('name', 'email', 'password')]);

        return redirect()->route('install.step', 'license');
    }

    private function handleLicense(Request $request)
    {
        $request->validate([
            'purchase_code' => 'required|string|max:255',
        ]);

        $code   = trim($request->input('purchase_code'));
        $appUrl = session('install_db.app_url', config('app.url'));
        $domain = parse_url($appUrl, PHP_URL_HOST) ?? request()->getHost();

        $result = $this->license->activate($code, $domain);

        if (isset($result['error'])) {
            return back()->withErrors(['purchase_code' => $result['error']])->withInput();
        }

        session(['install_license' => [
            'purchase_code' => $code,
            'license_key'   => $result['license_key'],
        ]]);

        return redirect()->route('install.step', 'finish');
    }

    private function handleFinish(Request $request)
    {
        $db      = session('install_db', []);
        $admin   = session('install_admin');
        $license = session('install_license', []);

        if (empty($license)) {
            return redirect()->route('install.step', 'license')
                ->withErrors(['purchase_code' => 'Please complete the license step.']);
        }

        if (empty($admin)) {
            return redirect()->route('install.step', 'admin')
                ->withErrors(['name' => 'Please complete the admin step.']);
        }

        // 1. Write .env and hot-swap DB (skip in test env — uses existing DB)
        if (!empty($db)) {
            $this->writeEnv($db, $license);
            $this->hotSwapDb($db);
            $appUrl = rtrim($db['app_url'], '/');
            config(['app.url' => $appUrl]);
            URL::forceRootUrl($appUrl);
            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        // 2. Migrate (fresh if existing tables, otherwise normal)
        try {
            if (!empty($db['has_tables'])) {
                Artisan::call('migrate:fresh', ['--force' => true]);
            } else {
                Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            return back()->withErrors(['migrate' => 'Database migration failed: ' . $e->getMessage()]);
        }

        // 3. Seed all default data via InstallSeeder (roles, languages, settings, pages, …)
        try {
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\InstallSeeder',
                '--force' => true,
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['migrate' => 'Seeding failed: ' . $e->getMessage()]);
        }

        // 4. Create admin user (email pre-verified — no welcome-email needed)
        $user = User::firstOrCreate(
            ['email' => $admin['email']],
            [
                'name'              => $admin['name'],
                'password'          => Hash::make($admin['password']),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );

        if ($user->wasRecentlyCreated === false) {
            $user->update([
                'email_verified_at' => $user->email_verified_at ?? now(),
                'status'            => 'active',
            ]);
        }

        $user->syncRoles(['admin']);

        // 6. Ensure storage dirs exist (shared hosting often lacks them)
        foreach (['framework/sessions', 'framework/cache/data', 'framework/views', 'logs', 'app'] as $dir) {
            $path = storage_path($dir);
            if (!is_dir($path)) {
                @mkdir($path, 0755, true);
            }
        }

        // 7. Attempt public storage symlink (may fail on restrictive shared hosting)
        try {
            if (!file_exists(public_path('storage'))) {
                Artisan::call('storage:link', ['--force' => true]);
            }
        } catch (\Throwable) {
            // Non-fatal — user can create symlink manually or copy public/storage
        }

        // 8. Clear caches
        try {
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
        } catch (\Throwable) {}

        // 9. Write install lock
        file_put_contents(storage_path('install.lock'), now()->toDateTimeString());

        session()->forget(['install_db', 'install_admin', 'install_license']);

        return redirect()->route('login')->with('success', __('flash.install_complete'));
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Bootstrap a minimal .env if none exists, so the session driver is `file`
     * (not `database`) during the install wizard — the DB doesn't exist yet.
     */
    private function bootstrapEnv(): void
    {
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            return;
        }

        $example = base_path('.env.example');
        $content = file_exists($example) ? file_get_contents($example) : '';

        // Generate a fresh APP_KEY
        $key = 'base64:' . base64_encode(random_bytes(32));

        // Replace or append APP_KEY
        if (preg_match('/^APP_KEY=.*/m', $content)) {
            $content = preg_replace('/^APP_KEY=.*/m', "APP_KEY={$key}", $content);
        } else {
            $content = "APP_KEY={$key}\n" . $content;
        }

        // Force file session during install (DB doesn't exist yet)
        if (preg_match('/^SESSION_DRIVER=.*/m', $content)) {
            $content = preg_replace('/^SESSION_DRIVER=.*/m', 'SESSION_DRIVER=file', $content);
        }

        // Disable session encryption until proper key is set
        if (preg_match('/^SESSION_ENCRYPT=.*/m', $content)) {
            $content = preg_replace('/^SESSION_ENCRYPT=.*/m', 'SESSION_ENCRYPT=false', $content);
        }

        EnvWriter::write($envPath, $content);

        // Hot-load the new key into config so it takes effect immediately
        config(['app.key' => $key]);
    }

    private function detectAppUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = preg_replace('#/install(/.*)?$#', '', $_SERVER['REQUEST_URI'] ?? '');
        $path = rtrim($path, '/');
        return $scheme . '://' . $host . $path;
    }

    private function runChecks(): array
    {
        return [
            'PHP >= 8.3'                => version_compare(PHP_VERSION, '8.3.0', '>='),
            'PDO'                       => extension_loaded('pdo'),
            'PDO MySQL'                 => extension_loaded('pdo_mysql'),
            'Mbstring'                  => extension_loaded('mbstring'),
            'OpenSSL'                   => extension_loaded('openssl'),
            'Tokenizer'                 => extension_loaded('tokenizer'),
            'JSON'                      => extension_loaded('json'),
            'BCMath'                    => extension_loaded('bcmath'),
            'Fileinfo'                  => extension_loaded('fileinfo'),
            'cURL'                      => extension_loaded('curl'),
            'GD (avatars & QR codes)'   => extension_loaded('gd'),
            'storage/ writable'         => is_writable(storage_path()),
            'bootstrap/cache/ writable' => is_writable(base_path('bootstrap/cache')),
            '.env writable'             => is_writable(base_path('.env')) || !file_exists(base_path('.env')),
        ];
    }

    private function hotSwapDb(array $db): void
    {
        config([
            'database.default'                    => 'mysql',
            'database.connections.mysql.host'     => $db['db_host'],
            'database.connections.mysql.port'     => $db['db_port'] ?? 3306,
            'database.connections.mysql.database' => $db['db_name'],
            'database.connections.mysql.username' => $db['db_user'],
            'database.connections.mysql.password' => $db['db_password'] ?? '',
        ]);
        DB::purge('mysql');
        DB::purge('sqlite');
        DB::reconnect('mysql');
    }

    private function writeEnv(array $db, array $license): void
    {
        $existing = file_exists(base_path('.env')) ? file_get_contents(base_path('.env')) : '';
        preg_match('/APP_KEY=(.+)/', $existing, $m);
        $appKey = !empty(trim($m[1] ?? '')) ? trim($m[1]) : 'base64:' . base64_encode(random_bytes(32));

        $appName      = EnvWriter::escapeValue($db['app_name'] ?? 'Dravion');
        $pass         = EnvWriter::escapeValue($db['db_password'] ?? '');
        $appUrl       = rtrim($db['app_url'] ?? $this->detectAppUrl(), '/');
        $secureCookie = str_starts_with($appUrl, 'https://') ? 'true' : 'false';
        $licenseKey   = $license['license_key'] ?? '';
        $purchaseCode = $license['purchase_code'] ?? '';

        $env = <<<ENV
APP_NAME={$appName}
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL={$appUrl}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST={$db['db_host']}
DB_PORT={$db['db_port']}
DB_DATABASE={$db['db_name']}
DB_USERNAME={$db['db_user']}
DB_PASSWORD={$pass}

CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE={$secureCookie}
SESSION_SAME_SITE=lax

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@example.com"
MAIL_FROM_NAME={$appName}

DRAVION_LICENSE_SERVER=https://apsbg.com/dravion-server
DRAVION_LICENSE_KEY={$licenseKey}
DRAVION_PURCHASE_CODE={$purchaseCode}
ENV;
        EnvWriter::write(base_path('.env'), $env);
    }

}
