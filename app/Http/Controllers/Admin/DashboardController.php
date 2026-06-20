<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $newThisMonth   = User::whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year)
                              ->count();
        $recentActivity = Activity::with('causer')
                              ->latest()
                              ->take(10)
                              ->get();

        $health = $this->systemHealth();

        return view('admin.dashboard', compact('newThisMonth', 'recentActivity', 'health'));
    }

    public function clearCache()
    {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        @unlink(storage_path('license.cache'));
        return back()->with('success', __('flash.cache_cleared'));
    }

    private function systemHealth(): array
    {
        $diskTotal   = @disk_total_space(storage_path()) ?: 0;
        $diskFree    = @disk_free_space(storage_path())  ?: 0;
        $diskUsedPct = $diskTotal > 0 ? round(($diskTotal - $diskFree) / $diskTotal * 100) : 0;

        $dbPath  = config('database.connections.sqlite.database');
        $dbSize  = ($dbPath && file_exists($dbPath)) ? filesize($dbPath) : 0;

        return [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_limit'    => ini_get('memory_limit'),
            'max_upload'      => ini_get('upload_max_filesize'),
            'disk_used_pct'   => $diskUsedPct,
            'disk_free_gb'    => $diskFree > 0 ? round($diskFree / 1024 / 1024 / 1024, 1) : 0,
            'db_size_kb'      => round($dbSize / 1024, 1),
            'cache_driver'      => config('cache.default'),
            'queue_driver'      => config('queue.default'),
            'scheduler_last_run'=> Cache::get('scheduler_last_run'),
            'cron_command'      => '* * * * * cd ' . base_path() . ' && php artisan schedule:run >> /dev/null 2>&1',
        ];
    }
}
