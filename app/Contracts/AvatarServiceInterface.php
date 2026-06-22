<?php

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface AvatarServiceInterface
{
    public function store(UploadedFile $file, ?string $oldPath = null): string;
}
