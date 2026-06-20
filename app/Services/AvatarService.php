<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AvatarService
{
    private const MAX_SIZE = 200;

    public static function store(UploadedFile $file, ?string $oldPath = null): string
    {
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $contents = file_get_contents($file->getRealPath());
        if ($contents === false) {
            throw new \RuntimeException('Could not read uploaded avatar file.');
        }

        $image = imagecreatefromstring($contents);
        if ($image === false) {
            throw new \RuntimeException('Uploaded file is not a valid image.');
        }

        $w = imagesx($image);
        $h = imagesy($image);

        if ($w > self::MAX_SIZE || $h > self::MAX_SIZE) {
            $ratio  = min(self::MAX_SIZE / $w, self::MAX_SIZE / $h);
            $newW   = (int) round($w * $ratio);
            $newH   = (int) round($h * $ratio);
            $resized = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newW, $newH, $w, $h);
            imagedestroy($image);
            $image = $resized;
        }

        $path = 'avatars/' . Str::random(40) . '.jpg';

        ob_start();
        imagejpeg($image, null, 85);
        $jpeg = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($path, $jpeg);

        return $path;
    }
}
