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

    public function test_storage_serve_route_is_registered(): void
    {
        $routes = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->filter(fn ($r) => $r->getName() === 'storage.serve');
        $this->assertGreaterThan(0, $routes->count(), 'storage.serve route must be registered in web.php');
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

    public function test_storage_serve_route_returns_file_when_symlink_missing(): void
    {
        // Write a real file to storage/app/public so the route can serve it
        $content = 'fake-image-data';
        $relative = 'avatars/test-serve.jpg';
        $dir = storage_path('app/public/avatars');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        file_put_contents(storage_path('app/public/' . $relative), $content);

        $response = $this->get('/storage/' . $relative);
        $response->assertOk();

        // Cleanup
        @unlink(storage_path('app/public/' . $relative));
    }

    public function test_storage_serve_route_returns_404_for_missing_file(): void
    {
        $this->get('/storage/avatars/nonexistent-file-xyz.jpg')->assertNotFound();
    }
}
