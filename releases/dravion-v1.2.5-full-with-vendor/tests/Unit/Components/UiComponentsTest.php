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

    // --- Accordion ---

    public function test_accordion_renders_item(): void
    {
        $view = $this->blade('<x-ui.accordion title="What is Dravion?">It is a SaaS kit.</x-ui.accordion>');
        $view->assertSee('What is Dravion?');
        $view->assertSee('It is a SaaS kit.');
    }

    // --- Tabs ---

    public function test_tabs_renders_tab_labels(): void
    {
        $view = $this->blade('<x-ui.tabs :tabs="$tabs">Tab content</x-ui.tabs>', [
            'tabs' => [['label' => 'General'], ['label' => 'Security']],
        ]);
        $view->assertSee('General');
        $view->assertSee('Security');
    }

    // --- Dialog ---

    public function test_dialog_renders_trigger_and_title(): void
    {
        $view = $this->blade('<x-ui.dialog title="Confirm Delete"><x-slot:trigger>Open</x-slot:trigger>Are you sure?</x-ui.dialog>');
        $view->assertSee('Confirm Delete');
        $view->assertSee('Open');
        $view->assertSee('Are you sure?');
    }

    // --- Dropdown Menu ---

    public function test_dropdown_renders_items(): void
    {
        $view = $this->blade('<x-ui.dropdown :items="$items"><x-slot:trigger>Options</x-slot:trigger></x-ui.dropdown>', [
            'items' => [['label' => 'Edit', 'href' => '#'], ['label' => 'Delete', 'href' => '#']],
        ]);
        $view->assertSee('Options');
        $view->assertSee('Edit');
        $view->assertSee('Delete');
    }

    // --- Tooltip ---

    public function test_tooltip_renders_text(): void
    {
        $view = $this->blade('<x-ui.tooltip text="Save changes"><button>Save</button></x-ui.tooltip>');
        $view->assertSee('Save changes');
        $view->assertSee('Save');
    }

    // --- Switch ---

    public function test_switch_renders(): void
    {
        $view = $this->blade('<x-ui.switch name="notifications" />');
        $view->assertSee('name="notifications"', false);
    }

    public function test_switch_renders_label(): void
    {
        $view = $this->blade('<x-ui.switch name="notifications" label="Enable notifications" />');
        $view->assertSee('Enable notifications');
    }

    // --- Toggle ---

    public function test_toggle_renders(): void
    {
        $view = $this->blade('<x-ui.toggle name="bold">B</x-ui.toggle>');
        $view->assertSee('B');
    }

    // --- Sheet ---

    public function test_sheet_renders_trigger_and_title(): void
    {
        $view = $this->blade('<x-ui.sheet title="Edit User"><x-slot:trigger>Edit</x-slot:trigger>Form here</x-ui.sheet>');
        $view->assertSee('Edit User');
        $view->assertSee('Edit');
        $view->assertSee('Form here');
    }

    // --- Pagination ---

    public function test_pagination_renders_page_links(): void
    {
        $view = $this->blade('<x-ui.pagination :current="2" :total="5" url="/users" />');
        $view->assertSee('href="/users?page=1"', false);
        $view->assertSee('href="/users?page=3"', false);
    }

    public function test_pagination_hides_prev_on_first_page(): void
    {
        $view = $this->blade('<x-ui.pagination :current="1" :total="3" url="/users" />');
        $view->assertDontSee('href="/users?page=0"', false);
    }

    // --- Toast ---

    public function test_toast_renders_message(): void
    {
        $view = $this->blade('<x-ui.toast message="Saved!" />');
        $view->assertSee('Saved!');
    }

    public function test_toast_success_variant(): void
    {
        $view = $this->blade('<x-ui.toast message="Done" variant="success" />');
        $view->assertSee('Done');
    }

    // --- Drawer ---

    public function test_drawer_renders_trigger_and_title(): void
    {
        $view = $this->blade('<x-ui.drawer title="Filters"><x-slot:trigger>Open</x-slot:trigger>Content</x-ui.drawer>');
        $view->assertSee('Filters');
        $view->assertSee('Open');
        $view->assertSee('Content');
    }

    // --- Hover Card ---

    public function test_hover_card_renders(): void
    {
        $view = $this->blade('<x-ui.hover-card><x-slot:trigger>@username</x-slot:trigger>Profile info here</x-ui.hover-card>');
        $view->assertSee('@username');
        $view->assertSee('Profile info here');
    }

    // --- Collapsible ---

    public function test_collapsible_renders(): void
    {
        $view = $this->blade('<x-ui.collapsible><x-slot:trigger>Show more</x-slot:trigger>Hidden content</x-ui.collapsible>');
        $view->assertSee('Show more');
        $view->assertSee('Hidden content');
    }

    // --- Alert Dialog ---

    public function test_alert_dialog_renders_trigger_and_message(): void
    {
        $view = $this->blade('<x-ui.alert-dialog title="Delete?" description="This cannot be undone."><x-slot:trigger>Delete</x-slot:trigger></x-ui.alert-dialog>');
        $view->assertSee('Delete?');
        $view->assertSee('This cannot be undone.');
        $view->assertSee('Delete');
    }

    public function test_alert_dialog_has_confirm_and_cancel(): void
    {
        $view = $this->blade('<x-ui.alert-dialog title="Sure?" description="Really?" confirm="Yes, delete" cancel="No"><x-slot:trigger>Go</x-slot:trigger></x-ui.alert-dialog>');
        $view->assertSee('Yes, delete');
        $view->assertSee('No');
    }

    // --- Slider ---

    public function test_slider_renders(): void
    {
        $view = $this->blade('<x-ui.slider name="volume" :value="50" />');
        $view->assertSee('type="range"', false);
        $view->assertSee('name="volume"', false);
    }

    // --- Aspect Ratio ---

    public function test_aspect_ratio_renders_slot(): void
    {
        $view = $this->blade('<x-ui.aspect-ratio ratio="16/9">Content</x-ui.aspect-ratio>');
        $view->assertSee('Content');
    }

    // --- Popover ---

    public function test_popover_renders_trigger_and_content(): void
    {
        $view = $this->blade('<x-ui.popover><x-slot:trigger>Click me</x-slot:trigger>Popover body</x-ui.popover>');
        $view->assertSee('Click me');
        $view->assertSee('Popover body');
    }

    // --- Toggle Group ---

    public function test_toggle_group_renders_options(): void
    {
        $view = $this->blade('<x-ui.toggle-group name="align" :options="$opts" />', [
            'opts' => ['left' => 'L', 'center' => 'C', 'right' => 'R'],
        ]);
        $view->assertSee('L');
        $view->assertSee('C');
        $view->assertSee('R');
    }

    // --- Input OTP ---

    public function test_input_otp_renders_correct_digit_count(): void
    {
        $view = $this->blade('<x-ui.input-otp name="otp" :digits="6" />');
        $view->assertSee('name="otp"', false);
    }

    // --- Scroll Area ---

    public function test_scroll_area_renders_slot(): void
    {
        $view = $this->blade('<x-ui.scroll-area height="200px">Long content here</x-ui.scroll-area>');
        $view->assertSee('Long content here');
    }

    // --- Menubar ---

    public function test_menubar_renders_menus(): void
    {
        $view = $this->blade('<x-ui.menubar :menus="$menus" />', [
            'menus' => [
                ['label' => 'File', 'items' => [['label' => 'New'], ['label' => 'Open']]],
                ['label' => 'Edit', 'items' => [['label' => 'Copy']]],
            ],
        ]);
        $view->assertSee('File');
        $view->assertSee('Edit');
        $view->assertSee('New');
        $view->assertSee('Copy');
    }

    // --- Navigation Menu ---

    public function test_navigation_menu_renders_links(): void
    {
        $view = $this->blade('<x-ui.navigation-menu :items="$items" />', [
            'items' => [
                ['label' => 'Dashboard', 'href' => '/dashboard'],
                ['label' => 'Users', 'href' => '/users'],
            ],
        ]);
        $view->assertSee('Dashboard');
        $view->assertSee('Users');
        $view->assertSee('href="/dashboard"', false);
    }

    public function test_navigation_menu_marks_active(): void
    {
        $view = $this->blade('<x-ui.navigation-menu :items="$items" active="/users" />', [
            'items' => [
                ['label' => 'Dashboard', 'href' => '/dashboard'],
                ['label' => 'Users', 'href' => '/users'],
            ],
        ]);
        $view->assertSee('Users');
    }

    // --- Context Menu ---

    public function test_context_menu_renders_trigger_and_items(): void
    {
        $view = $this->blade('<x-ui.context-menu :items="$items">Right-click here</x-ui.context-menu>', [
            'items' => [
                ['label' => 'Copy'],
                ['label' => 'Paste'],
            ],
        ]);
        $view->assertSee('Right-click here');
        $view->assertSee('Copy');
        $view->assertSee('Paste');
    }
}
