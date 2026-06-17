<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_disk_url_contains_storage_path(): void
    {
        $url = Storage::disk('public')->url('avatars/test.jpg');

        $this->assertStringContainsString('/storage/avatars/test.jpg', $url);
    }

    public function test_public_disk_serve_enabled(): void
    {
        $config = config('filesystems.disks.public');
        $this->assertTrue($config['serve'] ?? false, 'public disk must have serve:true for symlink-less hosting');
    }

    public function test_avatar_stored_on_public_disk(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg', 300, 300);
        $path = \App\Services\AvatarService::store($file);

        Storage::disk('public')->assertExists($path);
        $this->assertStringStartsWith('avatars/', $path);
        $this->assertStringEndsWith('.jpg', $path);
    }

    public function test_avatar_url_is_accessible(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);
        $path = \App\Services\AvatarService::store($file);

        $url = Storage::disk('public')->url($path);
        $this->assertNotEmpty($url);
        $this->assertStringContainsString('/storage/', $url);
        $this->assertStringContainsString('avatars/', $url);
    }
}
