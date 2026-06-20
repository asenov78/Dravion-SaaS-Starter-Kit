<?php

namespace Tests\Feature\Admin;

use App\Models\Language;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_search_endpoint_exists_and_returns_json(): void
    {
        $this->actingAs($this->admin)
            ->getJson('/admin/search?q=test')
            ->assertStatus(200)
            ->assertJsonStructure(['results']);
    }

    public function test_short_query_returns_empty(): void
    {
        $this->actingAs($this->admin)
            ->getJson('/admin/search?q=ab')
            ->assertStatus(200)
            ->assertJson(['results' => []]);
    }

    public function test_finds_user_by_name(): void
    {
        $user = User::factory()->create(['name' => 'Teodora Karoleva']);
        $user->assignRole('user');

        $this->actingAs($this->admin)
            ->getJson('/admin/search?q=Teodora')
            ->assertStatus(200)
            ->assertJsonFragment(['label' => 'Teodora Karoleva', 'group' => 'users']);
    }

    public function test_finds_user_by_email(): void
    {
        $user = User::factory()->create(['email' => 'teodora@example.com']);
        $user->assignRole('user');

        $this->actingAs($this->admin)
            ->getJson('/admin/search?q=teodora@')
            ->assertStatus(200)
            ->assertJsonFragment(['meta' => 'teodora@example.com']);
    }

    public function test_user_url_points_to_users_list_with_search(): void
    {
        $user = User::factory()->create(['name' => 'Teodora Karoleva']);
        $user->assignRole('user');

        $data = $this->actingAs($this->admin)
            ->getJson('/admin/search?q=Teodora')
            ->json('results');

        $userResult = collect($data)->firstWhere('group', 'users');
        $this->assertNotNull($userResult);
        $this->assertStringContainsString('/admin/users', $userResult['url']);
        $this->assertStringContainsString('search=', $userResult['url']);
        $this->assertStringNotContainsString('/edit', $userResult['url']);
    }

    public function test_finds_role_by_name(): void
    {
        $this->actingAs($this->admin)
            ->getJson('/admin/search?q=admin')
            ->assertStatus(200)
            ->assertJsonFragment(['label' => 'admin', 'group' => 'roles']);
    }

    public function test_finds_activity_by_description(): void
    {
        activity()->log('User logged in from Bulgaria');

        $this->actingAs($this->admin)
            ->getJson('/admin/search?q=Bulgaria')
            ->assertStatus(200)
            ->assertJsonFragment(['group' => 'activity']);
    }

    public function test_activity_url_points_to_filtered_activity_page(): void
    {
        activity()->log('Special event happened today');

        $data = $this->actingAs($this->admin)
            ->getJson('/admin/search?q=Special event')
            ->json('results');

        $actResult = collect($data)->firstWhere('group', 'activity');
        $this->assertNotNull($actResult);
        $this->assertStringContainsString('/admin/activity', $actResult['url']);
        $this->assertStringContainsString('search=', $actResult['url']);
    }

    public function test_pages_group_not_returned(): void
    {
        $data = $this->actingAs($this->admin)
            ->getJson('/admin/search?q=dashboard')
            ->json('results');

        $hasPages = collect($data)->contains('group', 'pages');
        $this->assertFalse($hasPages);
    }

    public function test_finds_setting_by_key(): void
    {
        Setting::set('site_name', 'Dravion Test');

        $this->actingAs($this->admin)
            ->getJson('/admin/search?q=site_name')
            ->assertStatus(200)
            ->assertJsonFragment(['group' => 'settings']);
    }

    public function test_finds_language_by_name(): void
    {
        Language::create(['name' => 'Bulgarian', 'code' => 'bg', 'flag' => '🇧🇬', 'is_default' => false]);

        $this->actingAs($this->admin)
            ->getJson('/admin/search?q=Bulgar')
            ->assertStatus(200)
            ->assertJsonFragment(['group' => 'languages']);
    }

    public function test_result_contains_url(): void
    {
        $user = User::factory()->create(['name' => 'Unique Person XYZ']);
        $user->assignRole('user');

        $data = $this->actingAs($this->admin)
            ->getJson('/admin/search?q=Unique Person')
            ->assertStatus(200)
            ->json('results');

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('url', $data[0]);
    }

    public function test_unauthenticated_cannot_search(): void
    {
        $this->getJson('/admin/search?q=test')
            ->assertStatus(302);
    }

    // --- Group filtering ---

    public function test_filter_by_group_users_only(): void
    {
        $user = User::factory()->create(['name' => 'FilterTest User']);
        $user->assignRole('user');
        activity()->log('FilterTest activity log');

        $data = $this->actingAs($this->admin)
            ->getJson('/admin/search?q=FilterTest&groups[]=users')
            ->json('results');

        $this->assertTrue(collect($data)->every(fn ($r) => $r['group'] === 'users'));
        $this->assertFalse(collect($data)->contains('group', 'activity'));
    }

    public function test_filter_by_multiple_groups(): void
    {
        $user = User::factory()->create(['name' => 'MultiTest User']);
        $user->assignRole('user');
        Setting::set('multitest_key', 'val');

        $data = $this->actingAs($this->admin)
            ->getJson('/admin/search?q=MultiTest&groups[]=users&groups[]=settings')
            ->json('results');

        $groups = collect($data)->pluck('group')->unique()->sort()->values()->all();
        $this->assertEqualsCanonicalizing(['settings', 'users'], $groups);
    }

    public function test_empty_groups_array_returns_all(): void
    {
        $user = User::factory()->create(['name' => 'EmptyGroup Test']);
        $user->assignRole('user');
        activity()->log('EmptyGroup activity');

        $data = $this->actingAs($this->admin)
            ->getJson('/admin/search?q=EmptyGroup&groups[]=')
            ->json('results');

        $groups = collect($data)->pluck('group')->unique()->all();
        $this->assertContains('users', $groups);
        $this->assertContains('activity', $groups);
    }

    public function test_no_groups_param_returns_all(): void
    {
        $user = User::factory()->create(['name' => 'AllGroups Test']);
        $user->assignRole('user');
        activity()->log('AllGroups activity');

        $data = $this->actingAs($this->admin)
            ->getJson('/admin/search?q=AllGroups')
            ->json('results');

        $groups = collect($data)->pluck('group')->unique()->all();
        $this->assertContains('users', $groups);
        $this->assertContains('activity', $groups);
    }

    // --- Search persistence ---

    public function test_header_prefills_query_from_url_search_param(): void
    {
        $html = $this->actingAs($this->admin)
            ->get('/admin/users?search=Teodora')
            ->assertStatus(200)
            ->getContent();

        $this->assertStringContainsString('Teodora', $html);
        // query init value in Alpine x-data should contain the search term
        $this->assertMatchesRegularExpression("/query:\s*'Teodora'/", $html);
    }

    public function test_header_empty_query_when_no_search_param(): void
    {
        $html = $this->actingAs($this->admin)
            ->get('/admin/users')
            ->assertStatus(200)
            ->getContent();

        $this->assertMatchesRegularExpression("/query:\s*''/", $html);
    }

    // --- Blur overlay & cache clear button ---

    public function test_header_contains_teleport_overlay_without_blur(): void
    {
        $html = $this->actingAs($this->admin)
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->getContent();

        $this->assertStringContainsString('x-teleport="body"', $html);
        $this->assertStringContainsString('focused', $html);
        $this->assertStringNotContainsString('backdrop-blur-sm', $html);
    }

    public function test_search_uses_route_helper_not_hardcoded_path(): void
    {
        $html = $this->actingAs($this->admin)
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->getContent();

        // Hardcoded '/admin/search' breaks subdirectory installs (e.g. /dravion/)
        $this->assertStringNotContainsString("fetch('/admin/search?", $html);
        $this->assertStringContainsString(route('admin.search'), $html);
    }

    public function test_header_contains_cache_clear_button(): void
    {
        $html = $this->actingAs($this->admin)
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->getContent();

        $this->assertStringContainsString(route('admin.cache.clear'), $html);
    }

    public function test_cache_clear_button_posts_and_redirects(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.cache.clear'))
            ->assertRedirect();
    }
}
