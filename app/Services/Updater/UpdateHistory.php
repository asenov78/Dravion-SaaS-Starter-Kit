<?php

namespace App\Services\Updater;

class UpdateHistory
{
    private string $path;

    public function __construct()
    {
        $this->path = storage_path('app/updates/history.json');
    }

    public function all(): array
    {
        if (! file_exists($this->path)) {
            return [];
        }
        $data = json_decode(file_get_contents($this->path), true);
        return is_array($data) ? $data : [];
    }

    public function append(string $fromVersion, string $toVersion, string $changelog = ''): void
    {
        $dir = dirname($this->path);
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        $history   = $this->all();
        $history[] = [
            'from'         => $fromVersion,
            'to'           => $toVersion,
            'changelog'    => $changelog,
            'installed_at' => now()->toIso8601String(),
        ];

        file_put_contents($this->path, json_encode($history, JSON_PRETTY_PRINT));
    }

    public function ensureExists(string $currentVersion): void
    {
        if (file_exists($this->path)) {
            return;
        }
        if (! file_exists(storage_path('install.lock'))) {
            return;
        }
        $this->append('—', $currentVersion);
    }
}
