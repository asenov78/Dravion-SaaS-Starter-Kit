<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstallController extends Controller
{
    private array $steps = ['requirements', 'database', 'admin', 'license', 'finish'];

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
            $data['detected_url'] = $this->detectAppUrl();
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
        return redirect()->route('install.step', 'database');
    }

    private function handleDatabase(Request $request)
    {
        $request->validate([
            'app_url'     => 'required|url',
            'db_host'     => 'required|string',
            'db_port'     => 'required|integer',
            'db_name'     => 'required|string',
            'db_user'     => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        try {
            new \PDO(
                "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_name};charset=utf8mb4",
                $request->db_user,
                $request->db_password ?? '',
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 5]
            );
        } catch (\PDOException $e) {
            return back()->withErrors(['db_host' => 'Connection failed: ' . $e->getMessage()])->withInput();
        }

        session(['install_db' => $request->only('app_url', 'db_host', 'db_port', 'db_name', 'db_user', 'db_password')]);

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

        $response = $this->callLicenseServer('activate', [
            'purchase_code' => $code,
            'domain'        => $domain,
        ]);

        if (! $response || isset($response['error'])) {
            $msg = $response['error'] ?? 'Could not reach license server. Check your purchase code.';
            return back()->withErrors(['purchase_code' => $msg])->withInput();
        }

        session(['install_license' => [
            'purchase_code' => $code,
            'license_key'   => $response['license_key'],
        ]]);

        return redirect()->route('install.step', 'finish');
    }

    private function callLicenseServer(string $endpoint, array $body): ?array
    {
        $url = rtrim(config('dravion.license_server', 'https://apsbg.com/dravion-server'), '/') . '/api/router.php?endpoint=' . $endpoint;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) return null;
        return json_decode($raw, true);
    }

    private function handleFinish(Request $request)
    {
        $db      = session('install_db', []);
        $admin   = session('install_admin');
        $license = session('install_license', []);

        // 1. Write .env and hot-swap DB (skip in test env — uses existing DB)
        if (!empty($db)) {
            $this->writeEnv($db, $license);
            $this->hotSwapDb($db);
        }

        // 2. Migrate
        Artisan::call('migrate', ['--force' => true]);

        // 3. Seed roles & permissions
        Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder',
            '--force' => true,
        ]);

        // 4. Create admin user
        $user = User::firstOrCreate(
            ['email' => $admin['email']],
            [
                'name'     => $admin['name'],
                'password' => Hash::make($admin['password']),
                'status'   => 'active',
            ]
        );
        $user->syncRoles(['admin']);

        // 5. Write install lock
        file_put_contents(storage_path('install.lock'), now()->toDateTimeString());

        session()->forget(['install_db', 'install_admin', 'install_license']);

        return redirect()->route('login')->with('success', 'Installation complete! Please sign in.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function detectAppUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // Strip /install and anything after it from the current URI
        $path = preg_replace('#/install(/.*)?$#', '', $_SERVER['REQUEST_URI'] ?? '');
        $path = rtrim($path, '/');
        return $scheme . '://' . $host . $path;
    }

    private function runChecks(): array
    {
        return [
            'PHP >= 8.2'                => version_compare(PHP_VERSION, '8.2.0', '>='),
            'PDO'                       => extension_loaded('pdo'),
            'PDO MySQL'                 => extension_loaded('pdo_mysql'),
            'Mbstring'                  => extension_loaded('mbstring'),
            'OpenSSL'                   => extension_loaded('openssl'),
            'Tokenizer'                 => extension_loaded('tokenizer'),
            'JSON'                      => extension_loaded('json'),
            'BCMath'                    => extension_loaded('bcmath'),
            'Fileinfo'                  => extension_loaded('fileinfo'),
            'storage/ writable'         => is_writable(storage_path()),
            'bootstrap/cache/ writable' => is_writable(base_path('bootstrap/cache')),
            '.env writable'             => is_writable(base_path('.env')) || !file_exists(base_path('.env')),
        ];
    }

    private function hotSwapDb(array $db): void
    {
        config([
            'database.connections.mysql.host'     => $db['db_host'],
            'database.connections.mysql.port'     => $db['db_port'] ?? 3306,
            'database.connections.mysql.database' => $db['db_name'],
            'database.connections.mysql.username' => $db['db_user'],
            'database.connections.mysql.password' => $db['db_password'] ?? '',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    private function writeEnv(array $db, array $license): void
    {
        $existing = file_exists(base_path('.env')) ? file_get_contents(base_path('.env')) : '';
        preg_match('/APP_KEY=(.+)/', $existing, $m);
        $appKey = !empty($m[1]) ? trim($m[1]) : 'base64:' . base64_encode(random_bytes(32));
        $pass       = addslashes($db['db_password'] ?? '');
        $appUrl     = rtrim($db['app_url'] ?? $this->detectAppUrl(), '/');
        $licenseKey = $license['license_key'] ?? '';
        $purchaseCode = $license['purchase_code'] ?? '';

        $env = <<<ENV
APP_NAME=Dravion
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL={$appUrl}

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
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false

DRAVION_LICENSE_KEY={$licenseKey}
DRAVION_PURCHASE_CODE={$purchaseCode}
ENV;
        file_put_contents(base_path('.env'), $env);
    }
}
