<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TranslationPagesTest extends TestCase
{
    use RefreshDatabase;

    private function seedLanguages(): void
    {
        Language::create(['code' => 'en', 'name' => 'English', 'flag' => '🇬🇧', 'is_default' => true]);
        $bg = Language::create(['code' => 'bg', 'name' => 'Bulgarian', 'flag' => '🇧🇬', 'is_default' => false]);

        $bgKeys = [
            // nav
            'nav.dashboard'          => 'Табло',
            'nav.users'              => 'Потребители',
            'nav.settings'           => 'Настройки',
            'nav.activity'           => 'Активност',
            'nav.languages'          => 'Езици',
            'nav.roles'              => 'Роли',
            'nav.license'            => 'Лиценз',
            'nav.main'               => 'Главни',
            // app
            'app.name'               => 'Име',
            'app.role'               => 'Роля',
            'app.status'             => 'Статус',
            'app.created_at'         => 'Създаден на',
            'app.edit'               => 'Редактирай',
            'app.no_results'         => 'Няма намерени резултати.',
            'app.active'             => 'Активен',
            'app.suspended'          => 'Спрян',
            'app.search_users'       => 'Търси по име или имейл…',
            'app.search_activity'    => 'Търси по описание…',
            'app.search_languages'   => 'Търси по ключ или стойност…',
            'app.search_command'     => 'Търси или въведи команда…',
            'app.search_global'      => 'Търси навсякъде… (⌘K)',
            'app.roles'              => 'Роли',
            'app.pages'              => 'Страници',
            'app.cancel'             => 'Отказ',
            // auth
            'auth.login'             => 'Вход',
            'auth.title_login'       => 'Влезте в Dravion',
            'auth.email'             => 'Имейл адрес',
            'auth.register'          => 'Регистрация',
            'auth.title_register'    => 'Създайте акаунт',
            'auth.title_forgot'      => 'Нулиране на паролата',
            'auth.send_link'         => 'Изпрати линк за нулиране',
            'auth.title_reset'       => 'Задайте нова парола',
            'auth.reset'             => 'Нулиране на паролата',
            // users
            'users.title'            => 'Потребители',
            'users.add'              => 'Добави потребител',
            'users.edit'             => 'Редактирай потребител',
            'users.account'          => 'Акаунт',
            'users.personal'         => 'Лична информация',
            'users.address'          => 'Адрес',
            'users.social'           => 'Социални мрежи',
            'users.save_changes'     => 'Запази промените',
            'users.password_hint'    => 'Оставете празно за без промяна',
            'users.new_user'         => 'Нов потребител',
            'users.create_user'      => 'Създай потребител',
            'users.user_info'        => 'Информация за потребителя',
            'users.select_role'      => 'Изберете роля',
            'users.suspend'          => 'Спри',
            'users.activate'         => 'Активирай',
            // settings
            'settings.title'         => 'Настройки',
            'settings.general'       => 'Общи',
            'settings.app_name'      => 'Име на приложението',
            'settings.save'          => 'Запази настройките',
            'settings.email'         => 'Имейл',
            'settings.activity_log'  => 'Дневник на активността',
            'settings.subtitle'      => 'Управление на конфигурацията на приложението',
            'settings.registration_desc' => 'Позволете на нови потребители да се регистрират сами',
            'settings.maintenance_desc'  => 'Блокирайте всички не-администраторски достъпи с 503 страница',
            'settings.activity_log_desc' => 'Изберете кои събития да се записват в дневника на активността.',
            'settings.license_key'       => 'Лицензен ключ',
            'settings.version'           => 'Версия',
            // dashboard
            'dashboard.subtitle'         => 'Преглед на вашата SaaS платформа',
            'dashboard.admins'           => 'Администратори',
            'dashboard.suspended_count'  => 'Спрени',
            'dashboard.recent_activity'  => 'Скорошна активност',
            // roles
            'roles.title'            => 'Роли и права',
            'roles.subtitle'         => 'Управлявайте ролите и задайте права чрез матрицата по-долу.',
            'roles.roles'            => 'Роли',
            'roles.add'              => 'Добави роля',
            'roles.matrix'           => 'Матрица с права',
            'roles.save'             => 'Запази правата',
            'roles.permission'       => 'Право',
            // activity
            'activity.title'         => 'Дневник на активността',
            'activity.subtitle'      => 'История на системната и потребителска активност',
            'activity.event'         => 'Събитие',
            'activity.description'   => 'Описание',
            'activity.user'          => 'Потребител',
            'activity.when'          => 'Кога',
            'activity.empty'         => 'Все още няма записана активност.',
            // languages
            'languages.title'        => 'Езици',
            'languages.subtitle'     => 'Управлявайте езиците на интерфейса и преводите.',
            'languages.add'          => 'Добави език',
            'languages.installed'    => 'Инсталирани езици',
            'languages.translations' => 'Преводи',
            // activity log descriptions
            'activity.log.user_created'        => 'Създаден потребител :name (:email)',
            'activity.log.user_updated'        => 'Обновен потребител :name (:email)',
            'activity.log.user_suspended'      => 'Спрян потребител :name (:email)',
            'activity.log.user_activated'      => 'Активиран потребител :name (:email)',
            'activity.log.role_created'        => "Ролята ':role' е създадена",
            'activity.log.role_deleted'        => "Ролята ':role' е изтрита",
            'activity.log.permissions_updated' => 'Матрицата с права е обновена от :name',
            'activity.log.settings_updated'    => 'Системните настройки са обновени от :name',
            'activity.log.profile_updated'     => 'Профилът на :name (:email) е обновен',
            'activity.log.user_logged_in'      => ':name влезе в системата',
            'activity.log.user_logged_out'     => ':name излезе от системата',
            // permissions
            'permissions.view users'        => 'Преглед на потребители',
            'permissions.create users'      => 'Създаване на потребители',
            'permissions.edit users'        => 'Редактиране на потребители',
            'permissions.delete users'      => 'Изтриване на потребители',
            'permissions.suspend users'     => 'Спиране на потребители',
            'permissions.view settings'     => 'Преглед на настройки',
            'permissions.edit settings'     => 'Редактиране на настройки',
            'permissions.view activity log' => 'Преглед на дневника',
            // flash messages
            'flash.user_created'            => 'Потребителят е създаден.',
            'flash.user_updated'            => 'Потребителят е обновен.',
            'flash.user_suspended'          => 'Потребителят е спрян.',
            'flash.user_activated'          => 'Потребителят е активиран.',
            'flash.settings_saved'          => 'Настройките са запазени.',
            'flash.permissions_saved'       => 'Правата са запазени.',
            'flash.role_created'            => 'Ролята е създадена.',
            'flash.role_deleted'            => 'Ролята е изтрита.',
            'flash.language_added'          => 'Езикът е добавен.',
            'flash.language_deleted'        => 'Езикът е изтрит.',
            'flash.language_updated'        => 'Езикът е обновен.',
            'flash.translation_saved'       => 'Преводът е запазен.',
            'flash.license_activated'       => 'Лицензът е активиран успешно.',
            'flash.license_removed'         => 'Лицензът е премахнат.',
            'flash.cache_cleared'           => 'Кешът е изчистен.',
            'flash.profile_updated'         => 'Профилът е обновен.',
            // languages/edit
            'languages.back'                => 'Назад',
            'languages.export_json'         => 'Експортирай JSON',
            'languages.search_placeholder'  => 'Търси по ключ или стойност…',
            'languages.save_page'           => 'Запази страницата',
            'languages.key'                 => 'Ключ',
            'languages.english'             => 'Английски',
            // license
            'license.title'          => 'Лиценз',
            'license.subtitle'       => 'Управлявайте лицензния ключ на Dravion',
            'license.status'         => 'Статус на лиценза',
            'license.no_key'         => 'Няма конфигуриран лицензен ключ',
            'license.activate'       => 'Активирайте лиценза',
            'license.enter_key'      => 'Въведете лицензен ключ',
            'license.enter_key_desc' => 'Въведете кода за покупка от Envato / вашия доставчик на лиценз.',
        ];

        foreach ($bgKeys as $key => $value) {
            $bg->lines()->create(['key' => $key, 'value' => $value]);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        app('translator')->setLoaded([]);
        app()->setLocale('bg');
    }

    private function admin(): User
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    // ── AUTH PAGES ──────────────────────────────────────────────────────────

    public function test_login_page_translates(): void
    {
        $this->seedLanguages();
        $this->get('/login')
            ->assertStatus(200)
            ->assertSee('Влезте в Dravion')
            ->assertSee('Имейл адрес');
    }

    public function test_register_page_translates(): void
    {
        $this->seedLanguages();
        $this->get('/register')
            ->assertStatus(200)
            ->assertSee('Създайте акаунт')
            ->assertSee('Регистрация');
    }

    public function test_forgot_password_page_translates(): void
    {
        $this->seedLanguages();
        $this->get('/forgot-password')
            ->assertStatus(200)
            ->assertSee('Нулиране на паролата')
            ->assertSee('Изпрати линк за нулиране');
    }

    public function test_reset_password_page_translates(): void
    {
        $this->seedLanguages();
        $this->get('/reset-password/fake-token')
            ->assertStatus(200)
            ->assertSee('Задайте нова парола');
    }

    // ── ADMIN PAGES ─────────────────────────────────────────────────────────

    public function test_admin_dashboard_translates(): void
    {
        $this->seedLanguages();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('Преглед на вашата SaaS платформа')
            ->assertSee('Администратори')
            ->assertSee('Скорошна активност');
    }

    public function test_admin_users_index_translates(): void
    {
        $this->seedLanguages();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/users')
            ->assertStatus(200)
            ->assertSee('Потребители')
            ->assertSee('Добави потребител')
            ->assertSee('Роля')
            ->assertSee('Статус')
            ->assertSee('Създаден на');
    }

    public function test_admin_users_edit_translates(): void
    {
        $this->seedLanguages();
        $user = User::factory()->create();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get("/admin/users/{$user->id}/edit")
            ->assertStatus(200)
            ->assertSee('Редактирай потребител')
            ->assertSee('Акаунт')
            ->assertSee('Лична информация')
            ->assertSee('Запази промените');
    }

    public function test_admin_users_create_translates(): void
    {
        $this->seedLanguages();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/users/create')
            ->assertStatus(200)
            ->assertSee('Нов потребител')
            ->assertSee('Акаунт')
            ->assertSee('Лична информация')
            ->assertSee('Адрес')
            ->assertSee('Създай потребител');
    }

    public function test_admin_roles_translates(): void
    {
        $this->seedLanguages();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/roles')
            ->assertStatus(200)
            ->assertSee('Роли и права')
            ->assertSee('Матрица с права')
            ->assertSee('Запази правата');
    }

    public function test_admin_activity_translates(): void
    {
        $this->seedLanguages();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/activity')
            ->assertStatus(200)
            ->assertSee('Дневник на активността')
            ->assertSee('История на системната и потребителска активност')
            ->assertSee('Все още няма записана активност.');
    }

    public function test_admin_settings_page_translates(): void
    {
        $this->seedLanguages();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/settings')
            ->assertStatus(200)
            ->assertSee('Настройки')
            ->assertSee('Общи')
            ->assertSee('Имейл')
            ->assertSee('Запази настройките')
            ->assertSee('Управление на конфигурацията на приложението')
            ->assertSee('Позволете на нови потребители да се регистрират сами')
            ->assertSee('Изберете кои събития да се записват в дневника на активността.');
    }

    public function test_admin_nav_sidebar_translates(): void
    {
        $this->seedLanguages();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('Табло')
            ->assertSee('Потребители')
            ->assertSee('Настройки')
            ->assertSee('Активност');
    }

    public function test_admin_languages_index_translates(): void
    {
        $this->seedLanguages();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/languages')
            ->assertStatus(200)
            ->assertSee('Езици')
            ->assertSee('Управлявайте езиците на интерфейса и преводите.')
            ->assertSee('Добави език')
            ->assertSee('Инсталирани езици');
    }

    public function test_admin_license_translates(): void
    {
        $this->seedLanguages();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/updates')
            ->assertStatus(200)
            ->assertSee('Лиценз')
            ->assertSee('Статус на лиценза')
            ->assertSee('Активирайте лиценза');
    }

    public function test_activity_descriptions_translated(): void
    {
        $this->seedLanguages();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->withSession(['locale' => 'bg'])
            ->post('/admin/users', [
                'name'     => 'Иван Иванов',
                'email'    => 'ivan@example.com',
                'password' => 'password123',
                'role'     => 'user',
            ]);

        app('translator')->setLoaded([]);

        $this->actingAs($admin)
            ->withSession(['locale' => 'bg'])
            ->get('/admin/activity')
            ->assertStatus(200)
            ->assertSee('Създаден потребител Иван Иванов (ivan@example.com)');
    }

    public function test_users_search_works_and_translates(): void
    {
        $this->seedLanguages();
        \App\Models\User::factory()->create(['name' => 'Иван Иванов', 'email' => 'ivan@test.com']);
        \App\Models\User::factory()->create(['name' => 'Петър Петров', 'email' => 'petar@test.com']);

        $response = $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/users?search=Иван')
            ->assertStatus(200)
            ->assertSee('Иван Иванов')
            ->assertDontSee('Петър Петров');

        $response->assertSee('Търси навсякъде');
    }

    public function test_activity_search_works_and_translates(): void
    {
        $this->seedLanguages();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->withSession(['locale' => 'bg'])
            ->post('/admin/users', [
                'name'     => 'Уникален Потребител',
                'email'    => 'unique@example.com',
                'password' => 'password123',
                'role'     => 'user',
            ]);

        app('translator')->setLoaded([]);

        $this->actingAs($admin)
            ->withSession(['locale' => 'bg'])
            ->get('/admin/activity?search=Уникален')
            ->assertStatus(200)
            ->assertSee('Уникален Потребител');
    }

    public function test_dashboard_activity_descriptions_translated(): void
    {
        $this->seedLanguages();
        $admin = $this->admin();

        $this->actingAs($admin)
            ->withSession(['locale' => 'bg'])
            ->post('/admin/users', [
                'name'     => 'Петър Петров',
                'email'    => 'petar@example.com',
                'password' => 'password123',
                'role'     => 'user',
            ]);

        app('translator')->setLoaded([]);

        $this->actingAs($admin)
            ->withSession(['locale' => 'bg'])
            ->get('/admin/dashboard')
            ->assertStatus(200)
            ->assertSee('Създаден потребител Петър Петров (petar@example.com)');
    }

    public function test_languages_edit_page_translates(): void
    {
        $this->seedLanguages();
        $bg = \App\Models\Language::where('code', 'bg')->first();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get("/admin/languages/{$bg->id}/edit")
            ->assertStatus(200)
            ->assertSee('Назад')
            ->assertSee('Запази страницата')
            ->assertSee('Ключ')
            ->assertSee('Търси по ключ или стойност');
    }

    public function test_permission_names_translated_in_roles(): void
    {
        $this->seedLanguages();
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/roles')
            ->assertStatus(200)
            ->assertSee('Преглед на потребители')
            ->assertSee('Редактиране на потребители');
    }

    public function test_suspended_badge_uses_correct_bg_word(): void
    {
        $this->seedLanguages();
        $suspended = \App\Models\User::factory()->create(['status' => 'suspended']);
        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/users')
            ->assertStatus(200)
            ->assertSee('Спрян')
            ->assertDontSee('Суспендиран');
    }

    public function test_flash_messages_translated(): void
    {
        $this->seedLanguages();
        $admin = $this->admin();
        $this->actingAs($admin)
            ->withSession(['locale' => 'bg'])
            ->post('/admin/users', [
                'name'     => 'Test User',
                'email'    => 'testuser@example.com',
                'password' => 'password123',
                'role'     => 'user',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Потребителят е създаден.');
    }

    public function test_license_no_duplicate_enter_key(): void
    {
        $this->seedLanguages();
        $response = $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/updates')
            ->assertStatus(200);
        $content = $response->getContent();
        $this->assertEquals(1, substr_count($content, 'Въведете лицензен ключ'),
            'enter_key translation must appear exactly once as label');
    }

    // ── ENGLISH FALLBACK ────────────────────────────────────────────────────

    public function test_pages_show_english_when_locale_en(): void
    {
        $this->seedLanguages();
        app()->setLocale('en');
        app('translator')->setLoaded([]);

        $this->get('/login')
            ->assertSee('Sign in to Dravion')
            ->assertSee('Email Address');
    }

    public function test_empty_bg_value_falls_back_to_english(): void
    {
        $this->seedLanguages();
        // nav.license not in bg seeds → should show EN 'License'
        app('translator')->setLoaded([]);

        $this->actingAs($this->admin())
            ->withSession(['locale' => 'bg'])
            ->get('/admin/settings')
            ->assertSee('License'); // EN fallback
    }
}
