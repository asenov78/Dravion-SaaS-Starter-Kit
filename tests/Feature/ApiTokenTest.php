<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTokenTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $u = User::factory()->create(['email_verified_at' => now()]);
        $u->assignRole('user');
        return $u;
    }

    // --- Page ---

    public function test_tokens_page_renders(): void
    {
        $this->actingAs($this->user())
            ->get('/api-tokens')
            ->assertStatus(200);
    }

    public function test_guest_redirected_from_tokens_page(): void
    {
        $this->get('/api-tokens')->assertRedirect('/login');
    }

    // --- Create ---

    public function test_user_can_create_token(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->post('/api-tokens', ['name' => 'My App Token'])
            ->assertRedirect('/api-tokens');

        $this->assertCount(1, $user->tokens);
        $this->assertEquals('My App Token', $user->tokens->first()->name);
    }

    public function test_token_name_required(): void
    {
        $this->actingAs($this->user())
            ->post('/api-tokens', ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_token_name_max_length(): void
    {
        $this->actingAs($this->user())
            ->post('/api-tokens', ['name' => str_repeat('a', 256)])
            ->assertSessionHasErrors('name');
    }

    public function test_plaintext_token_shown_once_in_session(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->post('/api-tokens', ['name' => 'Test Token'])
            ->assertRedirect('/api-tokens');

        $this->assertNotNull(session('new_token'));
    }

    // --- Revoke ---

    public function test_user_can_revoke_token(): void
    {
        $user  = $this->user();
        $token = $user->createToken('Old Token');

        $this->actingAs($user)
            ->delete("/api-tokens/{$token->accessToken->id}")
            ->assertRedirect('/api-tokens');

        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_cannot_revoke_other_users_token(): void
    {
        $user  = $this->user();
        $other = $this->user();
        $token = $other->createToken('Other Token');

        $this->actingAs($user)
            ->delete("/api-tokens/{$token->accessToken->id}")
            ->assertStatus(403);
    }

    // --- Revoke all ---

    public function test_user_can_revoke_all_tokens(): void
    {
        $user = $this->user();
        $user->createToken('Token 1');
        $user->createToken('Token 2');

        $this->actingAs($user)
            ->delete('/api-tokens')
            ->assertRedirect('/api-tokens');

        $this->assertCount(0, $user->fresh()->tokens);
    }
}
