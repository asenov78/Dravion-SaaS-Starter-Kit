<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClearCacheTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        return $user;
    }

    public function test_clear_cache_deletes_license_cache_file(): void
    {
        $cacheFile = storage_path('license.cache');
        file_put_contents($cacheFile, json_encode(['valid' => true, 'checked_at' => time()]));

        $this->actingAs($this->admin())
            ->post(route('admin.cache.clear'))
            ->assertRedirect();

        $this->assertFileDoesNotExist($cacheFile);
    }

    public function test_clear_cache_redirects_with_success(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.cache.clear'))
            ->assertRedirect()
            ->assertSessionHas('success');
    }
}
