<?php

namespace App\Services;

class EnvWriter
{
    /**
     * Write full .env content atomically with exclusive lock.
     */
    public static function write(string $path, string $content): void
    {
        $fh = fopen($path, 'c');
        if ($fh === false) {
            throw new \RuntimeException("Cannot open .env for writing: {$path}");
        }

        try {
            flock($fh, LOCK_EX);
            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, $content);
            fflush($fh);
        } finally {
            flock($fh, LOCK_UN);
            fclose($fh);
        }
    }

    /**
     * Set or update a single key in an existing .env file with exclusive lock.
     * Values containing spaces, $, #, or " are wrapped in double quotes.
     */
    public static function set(string $path, string $key, string $value): void
    {
        $fh = fopen($path, 'c+');
        if ($fh === false) {
            throw new \RuntimeException("Cannot open .env for writing: {$path}");
        }

        try {
            flock($fh, LOCK_EX);

            $content = stream_get_contents($fh);
            $escaped = self::escapeValue($value);

            if (preg_match("/^{$key}=.*/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$escaped}", $content);
            } else {
                $content = rtrim($content) . "\n{$key}={$escaped}\n";
            }

            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, $content);
            fflush($fh);
        } finally {
            flock($fh, LOCK_UN);
            fclose($fh);
        }
    }

    public static function escapeValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        // Quote if contains spaces, $, #, ", or \
        if (preg_match('/[\s$#"\\\\]/', $value)) {
            return '"' . str_replace('"', '\\"', $value) . '"';
        }

        return $value;
    }
}
