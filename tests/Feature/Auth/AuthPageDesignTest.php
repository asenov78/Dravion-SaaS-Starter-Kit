<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthPageDesignTest extends TestCase
{
    use RefreshDatabase;

    // ── Layout structure ──────────────────────────────────────────────────

    public function test_login_has_split_layout(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();
        // TailAdmin split: left form + right brand panel
        $this->assertStringContainsString('lg:flex-row', $html);
        $this->assertStringContainsString('lg:w-1/2', $html);
        $this->assertStringContainsString('bg-brand-950', $html);
    }

    public function test_register_has_split_layout(): void
    {
        $html = $this->get('/register')->assertOk()->getContent();
        $this->assertStringContainsString('lg:flex-row', $html);
        $this->assertStringContainsString('bg-brand-950', $html);
    }

    public function test_forgot_password_has_split_layout(): void
    {
        $html = $this->get('/forgot-password')->assertOk()->getContent();
        $this->assertStringContainsString('lg:flex-row', $html);
        $this->assertStringContainsString('bg-brand-950', $html);
    }

    public function test_reset_password_has_split_layout(): void
    {
        $html = $this->get('/reset-password/fake-token')->assertOk()->getContent();
        $this->assertStringContainsString('lg:flex-row', $html);
        $this->assertStringContainsString('bg-brand-950', $html);
    }

    // ── Password toggle ───────────────────────────────────────────────────

    public function test_login_has_password_toggle(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();
        $this->assertStringContainsString('showPassword', $html);
        $this->assertStringContainsString("showPassword ? 'text' : 'password'", $html);
    }

    public function test_register_has_password_toggle(): void
    {
        $html = $this->get('/register')->assertOk()->getContent();
        $this->assertStringContainsString('showPassword', $html);
    }

    // ── Forms have correct fields ─────────────────────────────────────────

    public function test_login_form_fields(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();
        $this->assertStringContainsString('name="email"', $html);
        $this->assertStringContainsString('name="password"', $html);
        $this->assertStringContainsString('name="_token"', $html);
    }

    public function test_register_form_fields(): void
    {
        $html = $this->get('/register')->assertOk()->getContent();
        $this->assertStringContainsString('name="name"', $html);
        $this->assertStringContainsString('name="email"', $html);
        $this->assertStringContainsString('name="password"', $html);
        $this->assertStringContainsString('name="password_confirmation"', $html);
    }

    public function test_reset_password_has_token_field(): void
    {
        $html = $this->get('/reset-password/abc123')->assertOk()->getContent();
        $this->assertStringContainsString('name="token"', $html);
        $this->assertStringContainsString('value="abc123"', $html);
    }

    // ── Dark/light theme toggler ──────────────────────────────────────────

    public function test_login_has_theme_toggler(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();
        $this->assertStringContainsString('$store.theme.toggle()', $html);
    }

    // ── No old dark card design ───────────────────────────────────────────

    public function test_login_no_old_inline_dark_background(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();
        $this->assertStringNotContainsString('background:#060d1a', $html);
        $this->assertStringNotContainsString('x-ui.net-bg', $html);
    }

    public function test_register_no_old_inline_dark_background(): void
    {
        $html = $this->get('/register')->assertOk()->getContent();
        $this->assertStringNotContainsString('background:#060d1a', $html);
    }

    // ── i18n ─────────────────────────────────────────────────────────────

    public function test_login_uses_i18n(): void
    {
        app()->setLocale('bg');
        $html = $this->get('/login')->assertOk()->getContent();
        $this->assertStringContainsString(__('auth.login', [], 'bg'), $html);
    }

    public function test_forgot_password_uses_i18n(): void
    {
        app()->setLocale('bg');
        $html = $this->get('/forgot-password')->assertOk()->getContent();
        $this->assertStringContainsString(__('auth.send_link', [], 'bg'), $html);
    }

    // ── Navigation links ─────────────────────────────────────────────────

    public function test_login_links_to_register(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();
        $this->assertStringContainsString('href="' . route('register') . '"', $html);
    }

    public function test_login_links_to_forgot_password(): void
    {
        $html = $this->get('/login')->assertOk()->getContent();
        $this->assertStringContainsString('href="' . route('password.request') . '"', $html);
    }

    public function test_register_links_to_login(): void
    {
        $html = $this->get('/register')->assertOk()->getContent();
        $this->assertStringContainsString('href="' . route('login') . '"', $html);
    }

    public function test_forgot_password_links_to_login(): void
    {
        $html = $this->get('/forgot-password')->assertOk()->getContent();
        $this->assertStringContainsString('href="' . route('login') . '"', $html);
    }
}
