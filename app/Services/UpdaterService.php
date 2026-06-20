<?php

namespace App\Services;

use App\Services\Updater\ReleaseDownloader;
use App\Services\Updater\ReleaseInstaller;
use App\Services\Updater\UpdateHistory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class UpdaterService
{
    public function __construct(
        private ReleaseDownloader $downloader,
        private ReleaseInstaller  $installer,
        private UpdateHistory     $history,
    ) {}

    public function getCurrentVersion(): string
    {
        return (string) config('dravion.version', '0.0.0');
    }

    /**
     * Fetch all published GitHub releases (newest first).
     *
     * @return array<int,array{tag:string,version:string,changelog:string,zip_url:string}>
     */
    public function getReleases(): array
    {
        $owner = config('updater.owner');
        $repo  = config('updater.repo');

        if (empty($owner) || empty($repo)) {
            return [];
        }

        $headers = ['Accept' => 'application/vnd.github+json', 'User-Agent' => 'Dravion-Updater'];
        if ($token = config('updater.token')) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        try {
            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->get("https://api.github.com/repos/{$owner}/{$repo}/releases?per_page=100");
        } catch (\Throwable) {
            return [];
        }

        if (! $response->successful() || ! is_array($response->json())) {
            return [];
        }

        $releases = [];
        foreach ($response->json() as $item) {
            if (! empty($item['draft'])) {
                continue;
            }
            $tag = (string) ($item['tag_name'] ?? '');
            if ($tag === '') {
                continue;
            }
            $body = (string) ($item['body'] ?? '');

            // Parse "requires: X.Y.Z" — declares the minimum predecessor version.
            // If set, this release cannot be installed unless currentVersion >= requires.
            preg_match('/^requires:\s*v?(\d+\.\d+\.\d+)/mi', $body, $m);
            $requires = $m[1] ?? null;

            $releases[] = [
                'tag'       => $tag,
                'version'   => ltrim($tag, 'vV'),
                'changelog' => $body,
                'zip_url'   => (string) ($item['zipball_url'] ?? ''),
                'requires'  => $requires,
            ];
        }

        usort($releases, fn ($a, $b) => version_compare($b['version'], $a['version']));

        return $releases;
    }

    /**
     * Latest published release, or null.
     *
     * @return array{tag:string,version:string,changelog:string,zip_url:string}|null
     */
    public function getLatestRelease(): ?array
    {
        return $this->getReleases()[0] ?? null;
    }

    /**
     * @return array{current:string,latest:?string,has_update:bool,changelog:?string,zip_url:?string,newer:array,older:array}
     */
    public function checkForUpdate(): array
    {
        $current  = $this->getCurrentVersion();
        $releases = $this->getReleases();

        if ($releases === []) {
            return [
                'current'    => $current,
                'latest'     => null,
                'has_update' => false,
                'changelog'  => null,
                'zip_url'    => null,
                'newer'      => [],
            ];
        }

        $latest = $releases[0];
        $newer  = array_values(array_filter(
            $releases,
            fn ($r) => version_compare($r['version'], $current, '>')
        ));
        $older = array_values(array_filter(
            $releases,
            fn ($r) => version_compare($r['version'], $current, '<=')
        ));

        // Mark each release in $newer as blocked if its requires > currentVersion.
        // Once one is blocked, all subsequent (older-ordered) ones are also blocked
        // because the chain is broken. $newer is sorted newest→oldest, so we
        // reverse to process oldest→newest and propagate blocking forward.
        $chainBroken = false;
        $newerOldestFirst = array_reverse($newer);
        foreach ($newerOldestFirst as &$rel) {
            if ($chainBroken) {
                $rel['blocked'] = true;
            } elseif ($rel['requires'] !== null
                && version_compare($rel['requires'], $current, '>')) {
                $rel['blocked']    = true;
                $chainBroken       = true;
            } else {
                $rel['blocked'] = false;
            }
        }
        unset($rel);
        $newer = array_reverse($newerOldestFirst); // restore newest→oldest

        // The next version to install is the oldest non-blocked release.
        $nextInstallable = null;
        foreach (array_reverse($newer) as $rel) { // oldest→newest
            if (! $rel['blocked']) {
                $nextInstallable = $rel;
                break;
            }
        }

        return [
            'current'          => $current,
            'latest'           => $latest['version'],
            'has_update'       => version_compare($latest['version'], $current, '>'),
            'changelog'        => $latest['changelog'],
            'zip_url'          => $latest['zip_url'],
            'newer'            => $newer,
            'older'            => $older,
            'next_installable' => $nextInstallable,
        ];
    }

    /**
     * Download and install a release ZIP.
     *
     * @return array{ok:bool,message:string,version?:string}
     */
    public function downloadAndInstall(string $zipUrl, string $changelog = ''): array
    {
        $workDir = config('updater.work_dir', storage_path('app/updates'));
        if (! is_dir($workDir)) {
            @mkdir($workDir, 0775, true);
        }

        $zipPath     = $workDir . '/release-' . time() . '.zip';
        $extractPath = $workDir . '/extract-' . time();

        try {
            $fromVersion = $this->getCurrentVersion();
            Artisan::call('down');

            $download = $this->downloader->download($zipUrl, $zipPath);
            if (! $download['ok']) {
                Artisan::call('up');
                return ['ok' => false, 'message' => $download['message']];
            }

            $install = $this->installer->install($zipPath, $extractPath, $changelog);
            if (! $install['ok']) {
                Artisan::call('up');
                return ['ok' => false, 'message' => $install['message']];
            }

            $this->history->append($fromVersion, $install['version'] ?? $fromVersion, $install['changelog']);

            Artisan::call('up');

            return ['ok' => true, 'message' => 'Update installed successfully.', 'version' => $install['version']];
        } catch (\Throwable $e) {
            Artisan::call('up');
            return ['ok' => false, 'message' => $e->getMessage()];
        } finally {
            @unlink($zipPath);
            $this->rrmdir($extractPath);
        }
    }

    public function ensureHistoryExists(): void
    {
        $this->history->ensureExists($this->getCurrentVersion());
    }

    public function getUpdateHistory(): array
    {
        return $this->history->all();
    }

    private function rrmdir(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
        }
        @rmdir($dir);
    }
}
