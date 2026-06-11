<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiComponentsTest extends TestCase
{
    use RefreshDatabase;

    // --- Button ---

    public function test_button_renders_default_variant(): void
    {
        $view = $this->blade('<x-ui.button>Save</x-ui.button>');
        $view->assertSee('Save');
        $view->assertSee('type="button"', false);
    }

    public function test_button_renders_danger_variant(): void
    {
        $view = $this->blade('<x-ui.button variant="danger">Delete</x-ui.button>');
        $view->assertSee('Delete');
    }

    public function test_button_accepts_type_submit(): void
    {
        $view = $this->blade('<x-ui.button type="submit">Go</x-ui.button>');
        $view->assertSee('type="submit"', false);
    }

    // --- Badge ---

    public function test_badge_renders_text(): void
    {
        $view = $this->blade('<x-ui.badge>admin</x-ui.badge>');
        $view->assertSee('admin');
    }

    public function test_badge_success_variant(): void
    {
        $view = $this->blade('<x-ui.badge variant="success">active</x-ui.badge>');
        $view->assertSee('active');
    }

    public function test_badge_danger_variant(): void
    {
        $view = $this->blade('<x-ui.badge variant="danger">suspended</x-ui.badge>');
        $view->assertSee('suspended');
    }

    // --- Card ---

    public function test_card_renders_slot(): void
    {
        $view = $this->blade('<x-ui.card>Card content</x-ui.card>');
        $view->assertSee('Card content');
    }

    public function test_card_renders_title_when_provided(): void
    {
        $view = $this->blade('<x-ui.card title="My Title">Body</x-ui.card>');
        $view->assertSee('My Title');
        $view->assertSee('Body');
    }

    // --- Input ---

    public function test_input_renders_with_name(): void
    {
        $view = $this->blade('<x-ui.input name="email" type="email" />');
        $view->assertSee('name="email"', false);
        $view->assertSee('type="email"', false);
    }

    public function test_input_shows_error_when_provided(): void
    {
        $view = $this->blade(
            '<x-ui.input name="email" :error="$err" />',
            ['err' => 'Email is required']
        );
        $view->assertSee('Email is required');
    }

    // --- Alert ---

    public function test_alert_renders_message(): void
    {
        $view = $this->blade('<x-ui.alert>Something went wrong</x-ui.alert>');
        $view->assertSee('Something went wrong');
    }

    public function test_alert_success_variant(): void
    {
        $view = $this->blade('<x-ui.alert variant="success">Done!</x-ui.alert>');
        $view->assertSee('Done!');
    }
}
