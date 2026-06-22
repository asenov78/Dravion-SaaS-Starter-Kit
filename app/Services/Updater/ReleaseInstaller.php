<?php

namespace App\Services\Updater;

use Illuminate\Support\Facades\Artisan;
use ZipArchive;

class ReleaseInstaller
{
    /**
     * Extract ZIP, copy files over base_path(), bump version, run migrations.
     *
     * @return array{ok:bool,message:string,version:?string,changelog:string}
     */
    public function install(string $zipPath, string $extractPath, string $changelog = ''): array
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            return ['ok' => false, 'message' => 'Could not open downloaded archive.', 'version' => null, 'changelog' => ''];
        }

        @mkdir($extractPath, 0775, true);
        $zip->extractTo($extractPath);
        $zip->close();

        $root = $this->locateExtractedRoot($extractPath);

        $this->copyTree($root, base_path(), (array) config('updater.protected_paths', []));

        $newVersion = $this->detectVersionFromExtract($root);
        if ($newVersion) {
            $this->writeVersionToConfig($newVersion);
        }

        $installedVersion  = $newVersion ?? (string) config('dravion.version', '0.0.0');
        $resolvedChangelog = $changelog ?: $this->detectChangelogFromExtract($root, $installedVersion);

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('lang:seed');
        @unlink(storage_path('license.cache'));

        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        return ['ok' => true, 'message' => 'Installed successfully.', 'version' => $installedVersion, 'changelog' => $resolvedChangelog];
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

            foreach ($protected as $p) {
                $p = trim($p, '/');
                if ($relative === $p || str_starts_with($relative, $p . '/')) {
                    continue 2;
                }
            }

            $target     = $to . DIRECTORY_SEPARATOR . $relative;
            $realTo     = realpath($to);
            $realTarget = realpath(dirname($target)) ?: dirname($target);

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

    private function detectChangelogFromExtract(string $root, string $version): string
    {
        $file = $root . '/CHANGELOG.md';
        if (! file_exists($file)) {
            return '';
        }
        $content = file_get_contents($file);
        $escaped = preg_quote($version, '/');
        if (preg_match('/^##\s+\[' . $escaped . '\][^\n]*\n(.*?)(?=^##\s+\[|\z)/ms', $content, $m)) {
            return trim($m[1]);
        }
        return '';
    }
}
