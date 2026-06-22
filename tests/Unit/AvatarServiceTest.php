<?php

namespace Tests\Unit;

use App\Services\AvatarService;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\TestCase;

class AvatarServiceTest extends TestCase
{
    public function test_rejects_file_exceeding_2mb(): void
    {
        // Create a fake file that reports a size > 2MB
        $file = $this->createMock(UploadedFile::class);
        $file->method('getSize')->willReturn(2 * 1024 * 1024 + 1);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/2MB/i');

        (new AvatarService())->store($file, null);
    }

    public function test_accepts_file_at_exactly_2mb(): void
    {
        // A file exactly at the limit should pass the size guard
        // (the invalid image check fires next — that's expected)
        $file = $this->createMock(UploadedFile::class);
        $file->method('getSize')->willReturn(2 * 1024 * 1024);
        $file->method('getRealPath')->willReturn('/dev/null');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/read|image/i');

        (new AvatarService())->store($file, null);
    }
}
