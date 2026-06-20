<?php

namespace App\Services\Updater;

use Illuminate\Support\Facades\Http;

class ReleaseDownloader
{
    /**
     * Download a release ZIP to a local file. Returns the local path on success.
     *
     * @return array{ok:bool,path:string,message:string}
     */
    public function download(string $zipUrl, string $destPath): array
    {
        $headers = ['User-Agent' => 'Dravion-Updater'];
        if ($token = config('updater.token')) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        try {
            $response = Http::withHeaders($headers)->timeout(120)->get($zipUrl);
        } catch (\Throwable $e) {
            return ['ok' => false, 'path' => '', 'message' => 'Download error: ' . $e->getMessage()];
        }

        if (! $response->successful()) {
            return ['ok' => false, 'path' => '', 'message' => 'Download failed (HTTP ' . $response->status() . ').'];
        }

        file_put_contents($destPath, $response->body());

        return ['ok' => true, 'path' => $destPath, 'message' => ''];
    }
}
