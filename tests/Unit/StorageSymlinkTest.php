<?php

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorageSymlinkTest extends TestCase
{
    use RefreshDatabase;

    private string $link;
    private string $target;

    protected function setUp(): void
    {
        parent::setUp();
        $this->link   = public_path('storage');
        $this->target = storage_path('app/public');
    }

    public function test_broken_symlink_is_removed_on_boot(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->markTestSkipped('Symlink creation requires admin on Windows');
        }

        $fakePath = sys_get_temp_dir() . '/dravion-nonexistent-' . uniqid();
        if (file_exists($this->link) || is_link($this->link)) {
            @unlink($this->link);
        }
        @symlink($fakePath, $this->link);

        $this->assertTrue(is_link($this->link), 'broken symlink should exist before boot');

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertFalse(
            is_link($this->link) && !is_dir($this->link),
            'Broken symlink was not removed by ensureStorageSymlink()'
        );
    }

    public function test_storage_serve_route_serves_file(): void
    {
        $dir = storage_path('app/public/avatars');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $file = $dir . '/route-test.jpg';
        file_put_contents($file, 'fake-jpg');

        $response = $this->get('/storage/avatars/route-test.jpg');
        $response->assertOk();

        @unlink($file);
    }

    public function test_storage_serve_route_404_for_missing_file(): void
    {
        $this->get('/storage/avatars/does-not-exist-xyz.jpg')->assertNotFound();
    }

    public function test_storage_url_contains_storage_segment(): void
    {
        $url = \Illuminate\Support\Facades\Storage::disk('public')->url('avatars/x.jpg');
        $this->assertStringContainsString('/storage/avatars/x.jpg', $url);
    }
}
