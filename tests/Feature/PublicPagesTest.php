<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_gallery_page_renders(): void
    {
        $this->get('/gallery')->assertStatus(200);
    }

    public function test_cms_page_renders_for_published_slug(): void
    {
        Page::create([
            'slug'         => 'about',
            'title'        => 'About',
            'is_published' => true,
            'show_in_nav'  => false,
            'content'      => '<p>About us</p>',
        ]);

        $this->get('/p/about')->assertStatus(200);
    }

    public function test_cms_page_returns_404_for_draft(): void
    {
        Page::create([
            'slug'         => 'draft-page',
            'title'        => 'Draft Page',
            'is_published' => false,
            'show_in_nav'  => false,
            'content'      => '<p>Draft</p>',
        ]);

        $this->get('/p/draft-page')->assertStatus(404);
    }

    public function test_cms_page_returns_404_for_unknown_slug(): void
    {
        $this->get('/p/nonexistent-page')->assertStatus(404);
    }

    public function test_contact_page_renders(): void
    {
        $this->get('/contact')->assertStatus(200);
    }

    public function test_contact_form_stores_message(): void
    {
        $this->post('/contact', [
            'name'    => 'John Doe',
            'email'   => 'john@example.com',
            'subject' => 'Hello',
            'message' => 'Test message content here.',
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_messages', [
            'email'   => 'john@example.com',
            'subject' => 'Hello',
        ]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $this->post('/contact', [])
            ->assertSessionHasErrors(['name', 'email', 'message']);
    }

    public function test_contact_form_validates_email_format(): void
    {
        $this->post('/contact', [
            'name'    => 'John',
            'email'   => 'not-an-email',
            'message' => 'Hello',
        ])->assertSessionHasErrors(['email']);
    }

    public function test_contact_form_rejects_oversized_message(): void
    {
        $this->post('/contact', [
            'name'    => 'John',
            'email'   => 'john@example.com',
            'message' => str_repeat('a', 3001),
        ])->assertSessionHasErrors(['message']);
    }
}
