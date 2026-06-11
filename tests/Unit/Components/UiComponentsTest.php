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

    // --- Separator ---

    public function test_separator_renders(): void
    {
        $view = $this->blade('<x-ui.separator />');
        $view->assertSee('hr', false);
    }

    public function test_separator_vertical(): void
    {
        $view = $this->blade('<x-ui.separator orientation="vertical" />');
        $view->assertSee('vertical');
    }

    // --- Avatar ---

    public function test_avatar_renders_initials(): void
    {
        $view = $this->blade('<x-ui.avatar name="John Doe" />');
        $view->assertSee('JD');
    }

    public function test_avatar_renders_image_when_src_provided(): void
    {
        $view = $this->blade('<x-ui.avatar name="Jane" src="/img/jane.jpg" />');
        $view->assertSee('/img/jane.jpg', false);
    }

    // --- Skeleton ---

    public function test_skeleton_renders(): void
    {
        $view = $this->blade('<x-ui.skeleton />');
        $view->assertSee('skeleton');
    }

    public function test_skeleton_accepts_class(): void
    {
        $view = $this->blade('<x-ui.skeleton class="h-4 w-32" />');
        $view->assertSee('h-4');
    }

    // --- Spinner ---

    public function test_spinner_renders(): void
    {
        $view = $this->blade('<x-ui.spinner />');
        $view->assertSee('svg', false);
    }

    // --- Progress ---

    public function test_progress_renders_value(): void
    {
        $view = $this->blade('<x-ui.progress :value="60" />');
        $view->assertSee('60%', false);
    }

    public function test_progress_clamps_to_100(): void
    {
        $view = $this->blade('<x-ui.progress :value="120" />');
        $view->assertSee('100%', false);
    }

    // --- Breadcrumb ---

    public function test_breadcrumb_renders_items(): void
    {
        $view = $this->blade('<x-ui.breadcrumb :items="$items" />', [
            'items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Users']],
        ]);
        $view->assertSee('Home');
        $view->assertSee('Users');
    }

    // --- Textarea ---

    public function test_textarea_renders_with_name(): void
    {
        $view = $this->blade('<x-ui.textarea name="bio" />');
        $view->assertSee('name="bio"', false);
        $view->assertSee('textarea', false);
    }

    public function test_textarea_shows_error(): void
    {
        $view = $this->blade('<x-ui.textarea name="bio" error="Required" />');
        $view->assertSee('Required');
    }

    // --- Checkbox ---

    public function test_checkbox_renders(): void
    {
        $view = $this->blade('<x-ui.checkbox name="agree" />');
        $view->assertSee('type="checkbox"', false);
        $view->assertSee('name="agree"', false);
    }

    public function test_checkbox_renders_label(): void
    {
        $view = $this->blade('<x-ui.checkbox name="agree" label="I agree" />');
        $view->assertSee('I agree');
    }

    // --- Select ---

    public function test_select_renders_options(): void
    {
        $view = $this->blade('<x-ui.select name="role" :options="$opts" />', [
            'opts' => ['admin' => 'Admin', 'user' => 'User'],
        ]);
        $view->assertSee('Admin');
        $view->assertSee('User');
        $view->assertSee('name="role"', false);
    }

    // --- Radio Group ---

    public function test_radio_group_renders_options(): void
    {
        $view = $this->blade('<x-ui.radio-group name="size" :options="$opts" />', [
            'opts' => ['sm' => 'Small', 'lg' => 'Large'],
        ]);
        $view->assertSee('Small');
        $view->assertSee('Large');
        $view->assertSee('type="radio"', false);
    }

    // --- Table ---

    public function test_table_renders_headers_and_rows(): void
    {
        $view = $this->blade('<x-ui.table :headers="$h" :rows="$r" />', [
            'h' => ['Name', 'Email'],
            'r' => [['Alice', 'alice@test.com']],
        ]);
        $view->assertSee('Name');
        $view->assertSee('Alice');
        $view->assertSee('alice@test.com');
    }

    // --- Kbd ---

    public function test_kbd_renders(): void
    {
        $view = $this->blade('<x-ui.kbd>Ctrl+K</x-ui.kbd>');
        $view->assertSee('Ctrl+K');
        $view->assertSee('kbd', false);
    }
}
