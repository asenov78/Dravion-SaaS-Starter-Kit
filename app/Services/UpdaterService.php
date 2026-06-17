<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class UpdaterService
{
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
            $releases[] = [
                'tag'       => $tag,
                'version'   => ltrim($tag, 'vV'),
                'changelog' => (string) ($item['body'] ?? ''),
                'zip_url'   => (string) ($item['zipball_url'] ?? ''),
            ];
        }

        // Sort by semver descending — GitHub API orders by publish date, not version.
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
     * Compare current version against published releases. `newer` holds every
     * release newer than the current version (newest first) with its changelog.
     *
     * @return array{current:string,latest:?string,has_update:bool,changelog:?string,zip_url:?string,newer:array<int,array{version:string,tag:string,changelog:string,zip_url:string}>}
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

        return [
            'current'    => $current,
            'latest'     => $latest['version'],
            'has_update' => version_compare($latest['version'], $current, '>'),
            'changelog'  => $latest['changelog'],
            'zip_url'    => $latest['zip_url'],
            'newer'      => $newer,
            'older'      => $older,
        ];
    }

    /**
     * Download a release ZIP, extract it, copy non-protected files over the
     * installation, run migrations and clear caches.
     *
     * @return array{ok:bool,message:string}
     */
    public function downloadAndInstall(string $zipUrl): array
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

            // Download
            $headers = ['User-Agent' => 'Dravion-Updater'];
            if ($token = config('updater.token')) {
                $headers['Authorization'] = 'Bearer ' . $token;
            }
            $response = Http::withHeaders($headers)->timeout(120)->get($zipUrl);
            if (! $response->successful()) {
                Artisan::call('up');
                return ['ok' => false, 'message' => 'Download failed (HTTP ' . $response->status() . ').'];
            }
            file_put_contents($zipPath, $response->body());

            // Extract
            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                Artisan::call('up');
                return ['ok' => false, 'message' => 'Could not open downloaded archive.'];
            }
            @mkdir($extractPath, 0775, true);
            $zip->extractTo($extractPath);
            $zip->close();

            // GitHub zipballs wrap files in a single top-level folder.
            $root = $this->locateExtractedRoot($extractPath);

            // Copy files, skipping protected paths.
            $this->copyTree($root, base_path(), (array) config('updater.protected_paths', []));

            // Write new version into the protected config/dravion.php BEFORE
            // cache clear — so getCurrentVersion() returns the new version immediately.
            $newVersion = $this->detectVersionFromExtract($root);
            if ($newVersion) {
                $this->writeVersionToConfig($newVersion);
            }

            // Migrate + clear caches
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            @unlink(storage_path('license.cache'));

            // Reset opcache so updated PHP files are served immediately.
            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }

            $installedVersion = $newVersion ?? $this->getCurrentVersion();
            $this->appendToHistory($fromVersion, $installedVersion);

            Artisan::call('up');

            return ['ok' => true, 'message' => 'Update installed successfully.', 'version' => $installedVersion];
        } catch (\Throwable $e) {
            Artisan::call('up');
            return ['ok' => false, 'message' => $e->getMessage()];
        } finally {
            @unlink($zipPath);
            $this->rrmdir($extractPath);
        }
    }

    /**
     * Bootstrap history.json if it doesn't exist yet — records the currently
     * running version as "installed" so the accordion isn't empty after a
     * manual ZIP deploy that predates history tracking.
     */
    public function ensureHistoryExists(): void
    {
        $path = storage_path('app/updates/history.json');
        if (file_exists($path)) {
            return;
        }
        // Only seed if the installer has already run (install.lock present).
        if (! file_exists(storage_path('install.lock'))) {
            return;
        }
        $this->appendToHistory('—', $this->getCurrentVersion());
    }

    public function getUpdateHistory(): array
    {
        $path = storage_path('app/updates/history.json');
        if (! file_exists($path)) {
            return [];
        }
        $data = json_decode(file_get_contents($path), true);
        return is_array($data) ? $data : [];
    }

    private function appendToHistory(string $fromVersion, string $toVersion): void
    {
        $dir = storage_path('app/updates');
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $history   = $this->getUpdateHistory();
        $history[] = [
            'from'         => $fromVersion,
            'to'           => $toVersion,
            'installed_at' => now()->toIso8601String(),
        ];
        file_put_contents($dir . '/history.json', json_encode($history, JSON_PRETTY_PRINT));
    }

    private function detectVersionFromExtract(string $root): ?string
    {
        $configFile = $root . '/config/dravion.php';
        if (! file_exists($configFile)) {
            return null;
        }
        $content = file_get_contents($configFile);
        if (preg_match("/'version'\s*=>\s*'([^']+)'/", $content, $m)) {
            return $m[1];
        }
        return null;
    }

    private function writeVersionToConfig(string $version): void
    {
        $configFile = base_path('config/dravion.php');
        if (! file_exists($configFile)) {
            return;
        }
        $content = file_get_contents($configFile);
        $updated = preg_replace(
            "/'version'\s*=>\s*'[^']+'/",
            "'version' => '{$version}'",
            $content
        );
        if ($updated && $updated !== $content) {
            file_put_contents($configFile, $updated);
        }
    }

    private function locateExtractedRoot(string $extractPath): string
    {
        $entries = array_values(array_filter(
            scandir($extractPath) ?: [],
            fn ($e) => $e !== '.' && $e !== '..'
        ));

        if (count($entries) === 1 && is_dir($extractPath . '/' . $entries[0])) {
            return $extractPath . '/' . $entries[0];
        }

        return $extractPath;
    }

    private function copyTree(string $from, string $to, array $protected): void
    {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($from, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            $relative = ltrim(str_replace($from, '', $item->getPathname()), '\\/');
            $relative = str_replace('\\', '/', $relative);

            // Skip protected paths.
            foreach ($protected as $p) {
                $p = trim($p, '/');
                if ($relative === $p || str_starts_with($relative, $p . '/')) {
                    continue 2;
                }
            }

            $target   = $to . DIRECTORY_SEPARATOR . $relative;
            $realTo   = realpath($to);
            $realTarget = realpath(dirname($target)) ?: dirname($target);

            // Reject path traversal — target must stay inside base_path.
            if ($realTo && ! str_starts_with($realTarget . DIRECTORY_SEPARATOR, $realTo . DIRECTORY_SEPARATOR)) {
                continue;
            }

            if ($item->isDir()) {
                if (! is_dir($target)) {
                    @mkdir($target, 0775, true);
                }
            } else {
                @copy($item->getPathname(), $target);
            }
        }
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
