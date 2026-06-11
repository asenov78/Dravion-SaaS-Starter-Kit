<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstallController extends Controller
{
    // Steps: requirements → database → admin → license → finish
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
        return view("install.{$step}", ['steps' => $this->steps, 'current' => $step]);
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

    private function handleRequirements(Request $request)
    {
        // Auto-pass if all requirements met
        return redirect()->route('install.step', 'database');
    }

    private function handleDatabase(Request $request)
    {
        $request->validate([
            'db_host'     => 'required',
            'db_name'     => 'required',
            'db_user'     => 'required',
            'db_password' => 'nullable',
        ]);

        // Test connection
        try {
            $pdo = new \PDO(
                "mysql:host={$request->db_host};dbname={$request->db_name};charset=utf8mb4",
                $request->db_user,
                $request->db_password ?? '',
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PDOException $e) {
            return back()->withErrors(['db_host' => 'Connection failed: ' . $e->getMessage()]);
        }

        // Store in session, write .env on finish
        session([
            'install_db' => $request->only('db_host', 'db_name', 'db_user', 'db_password'),
        ]);

        return redirect()->route('install.step', 'admin');
    }

    private function handleAdmin(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        session(['install_admin' => $request->only('name', 'email', 'password')]);

        return redirect()->route('install.step', 'license');
    }

    private function handleLicense(Request $request)
    {
        $request->validate([
            'purchase_code' => 'required|string',
        ]);

        session(['install_license' => $request->only('purchase_code')]);

        return redirect()->route('install.step', 'finish');
    }

    private function handleFinish(Request $request)
    {
        // TODO: write .env, run migrations, create admin, activate license, lock installer
        // Implemented in Slice: Installer
        return view('install.finish', ['steps' => $this->steps, 'current' => 'finish']);
    }
}
