<?php

namespace App\Console\Commands;

use App\Services\UpdaterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CheckReleasesCommand extends Command
{
    protected $signature   = 'updates:check-releases';
    protected $description = 'Fetch latest GitHub release and cache it for the sidebar badge.';

    public function handle(UpdaterService $updater): int
    {
        $latest = $updater->getLatestRelease();

        if ($latest === null) {
            $this->warn('Could not reach GitHub or no releases found.');
            return self::FAILURE;
        }

        Cache::put('github_latest_version', $latest['version'], now()->addHours(6));

        $this->info("Latest release: v{$latest['version']} — cached for 6 hours.");
        return self::SUCCESS;
    }
}
