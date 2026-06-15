<?php
namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'home', 'show_in_nav' => false, 'sort_order' => 0,
                'title' => 'Home', 'excerpt' => 'Welcome to our platform.',
                'content' => '<h2>Welcome</h2><p>This is the home page. Edit it from the admin panel.</p>',
                'hero_title' => 'Build Something Amazing', 'hero_subtitle' => 'The modern SaaS starter kit for Laravel developers.',
                'hero_cta_label' => 'Get Started',
            ],
            [
                'slug' => 'about', 'show_in_nav' => true, 'sort_order' => 1,
                'title' => 'About', 'excerpt' => 'Learn more about us.',
                'content' => '<h2>About Us</h2><p>We build great software. Edit this page from the admin panel.</p>',
            ],
            [
                'slug' => 'pricing', 'show_in_nav' => true, 'sort_order' => 2,
                'title' => 'Pricing', 'excerpt' => 'Simple, transparent pricing.',
                'content' => '<h2>Pricing</h2><p>Choose the plan that works for you. Edit from admin panel.</p>',
            ],
            [
                'slug' => 'contact', 'show_in_nav' => true, 'sort_order' => 3,
                'title' => 'Contact', 'excerpt' => 'Get in touch with us.',
                'content' => '<h2>Contact Us</h2><p>We would love to hear from you. Fill out the form below and we will get back to you as soon as possible.</p>',
                'hero_title' => 'Get In Touch', 'hero_subtitle' => 'Have a question or want to work together? We are here to help.',
                'hero_cta_label' => 'Send Message',
            ],
            [
                'slug' => 'gallery', 'show_in_nav' => true, 'sort_order' => 4,
                'title' => 'Gallery', 'excerpt' => 'Browse our gallery.',
                'content' => '<h2>Gallery</h2><p>Explore our work and portfolio.</p>',
                'hero_title' => 'Our Gallery', 'hero_subtitle' => 'A showcase of our best work and projects.',
                'hero_cta_label' => 'View All',
            ],
        ];

        foreach ($pages as $data) {
            $page = Page::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_published' => true])
            );
        }

        $homeContentEn = <<<'HTML'
<p style="font-size:1.1rem;color:#374151;line-height:1.8;margin-bottom:2rem;">Dravion is a <strong>production-ready SaaS starter kit</strong> built on Laravel 13 and PHP 8.3. Skip months of boilerplate and focus on your business logic — authentication, roles, notifications, CMS, licensing, self-updater, and 38+ UI components are all included out of the box.</p>

<h2>Everything You Need</h2>
<p style="color:#6b7280;margin-bottom:1.5rem;">No more reinventing the wheel. Every feature you need to launch a SaaS product is ready to use.</p>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;margin:1.5rem 0;">
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">👥</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">User Management</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Full CRUD, bulk actions, suspend/activate, export CSV, role assignment — everything with activity log.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🛡️</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Role-Based Access Control</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Spatie laravel-permission: admin, manager, editor, user. Fine-grained permission gates on every route.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🔑</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">API Tokens</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Laravel Sanctum personal access tokens. Create, name, and revoke tokens from the user interface.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">📋</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Activity Log</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Spatie activitylog: every action is recorded. Filters by user, event, and date. Export CSV.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🔔</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">In-App Notifications</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Bell icon with unread badge. JSON feed, mark as read, mark all. Auto-notifications for suspend/activate, new users, and updates.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">⬇️</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Self-Updater</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">GitHub Releases integration. Check for new version, download, install — no manual work. Protected paths.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">⚙️</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">App Settings</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">App name, SMTP configuration with test, maintenance mode, broadcast banner, default language — all from the UI.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🌍</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Multi-Language</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">lang/en + lang/bg. Admin UI for adding locales, inline key editing, import/export PHP files.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">📄</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">CMS Pages</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Manage public pages from the admin panel. Title, slug, HTML content, SEO meta, navigation order.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🧩</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">38+ UI Components</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Button, Card, Badge, Modal, Drawer, Toast, Table, Pagination, Tabs, Accordion, Input, Select and more — all shadcn/ui parity.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🖥️</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Session Management</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">View all active sessions with IP and browser. Logout other devices with password confirmation. Database sessions.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🔐</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Licensing</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">HMAC-based license validation against a license server. Weekly ping, domain lock, DEV bypass, license warning banner.</p>
  </div>
</div>

<hr>

<h2>Enterprise-Grade Security</h2>
<p style="color:#6b7280;margin-bottom:1rem;">Every route is protected. Rate limiting on all auth endpoints. Verified email middleware. Double permission check — route + controller.</p>
<ul>
  <li>Rate limiting: login 5/min, register 3/min, forgot-password 3/min</li>
  <li>Email verification with signed URLs (MustVerifyEmail)</li>
  <li>Suspended users blocked before session creation</li>
  <li>Session regeneration on login and privilege change</li>
  <li>CSRF protection on all forms and API endpoints</li>
  <li>Avatar upload: MIME validation + GD re-encode (no payload bypass)</li>
</ul>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin:1.5rem 0;">
  <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:1rem;text-align:center;">
    <div style="font-size:1.75rem;font-weight:800;color:#16a34a;">414</div>
    <div style="font-size:.75rem;color:#6b7280;margin-top:.25rem;">Passing Tests</div>
  </div>
  <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:1rem;text-align:center;">
    <div style="font-size:1.75rem;font-weight:800;color:#16a34a;">0</div>
    <div style="font-size:.75rem;color:#6b7280;margin-top:.25rem;">Known CVEs</div>
  </div>
  <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:1rem;text-align:center;">
    <div style="font-size:1.75rem;font-weight:800;color:#d97706;">2FA</div>
    <div style="font-size:.75rem;color:#6b7280;margin-top:.25rem;">Coming Soon</div>
  </div>
  <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:1rem;text-align:center;">
    <div style="font-size:1.75rem;font-weight:800;color:#2563eb;">100%</div>
    <div style="font-size:.75rem;color:#6b7280;margin-top:.25rem;">Route Protected</div>
  </div>
</div>

<hr>

<h2>Modern Technology Stack</h2>
<p style="color:#6b7280;margin-bottom:1rem;">Only proven, production-ready technologies. No experimental dependencies.</p>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:1rem;margin:1.5rem 0;">
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">Laravel 13</div>
    <div style="font-size:.75rem;color:#9ca3af;">PHP Framework</div>
  </div>
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">PHP 8.3</div>
    <div style="font-size:.75rem;color:#9ca3af;">Backend</div>
  </div>
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">Tailwind v4</div>
    <div style="font-size:.75rem;color:#9ca3af;">CSS</div>
  </div>
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">Alpine.js v3</div>
    <div style="font-size:.75rem;color:#9ca3af;">Reactivity</div>
  </div>
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">Sanctum</div>
    <div style="font-size:.75rem;color:#9ca3af;">API Auth</div>
  </div>
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">Spatie</div>
    <div style="font-size:.75rem;color:#9ca3af;">Roles &amp; Logs</div>
  </div>
</div>

<hr>

<h2>More Features Worth Mentioning</h2>
<ul>
  <li><strong>Global Search</strong> — Real-time search across users, roles, settings, activity log. 3+ characters → results.</li>
  <li><strong>Broadcast Banner</strong> — Site-wide messages to all users. Dismissable per session. Managed from Settings.</li>
  <li><strong>Dark / Light Mode</strong> — Saved in localStorage. No flash on load. Works on every component.</li>
  <li><strong>Installer Wizard</strong> — Multi-step install: DB, admin account, license, settings. Self-locks after installation.</li>
</ul>

<hr>

<p style="font-size:1rem;color:#374151;text-align:center;padding:1rem 0;">Ready to ship? Install, configure, and go live. No months of boilerplate — just your product.</p>
HTML;

        $homeContentBg = <<<'HTML'
<p style="font-size:1.1rem;color:#374151;line-height:1.8;margin-bottom:2rem;">Dravion е <strong>готов за продукция SaaS стартов комплект</strong>, изграден на Laravel 13 и PHP 8.3. Спестете месеци boilerplate и се фокусирайте върху бизнес логиката — автентикация, роли, нотификации, CMS, лицензиране, само-обновяване и 38+ UI компонента са включени от кутията.</p>

<h2>Всичко, което ви трябва</h2>
<p style="color:#6b7280;margin-bottom:1.5rem;">Не губете повече време в повтарящи се задачи. Всяка функция, необходима за стартиране на SaaS продукт, е готова за употреба.</p>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;margin:1.5rem 0;">
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">👥</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Управление на потребители</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Пълен CRUD, bulk actions, suspend/activate, export CSV, задаване на роли — всичко с activity log.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🛡️</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Ролева система</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Spatie laravel-permission: admin, manager, editor, user. Fine-grained permission gates на всеки route.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🔑</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">API Токени</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Laravel Sanctum personal access tokens. Създавай, именувай и отменяй токени от потребителския интерфейс.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">📋</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Activity Log</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Spatie activitylog: всяко действие е записано. Filters по потребител, събитие и дата. Export CSV.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🔔</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">In-app Нотификации</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Bell icon с unread badge. JSON feed, mark as read, mark all. Автоматични нотификации при suspend/activate, нов потребител и обновяване.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">⬇️</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Само-обновяване</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">GitHub Releases интеграция. Проверка за нова версия, сваляне, инсталиране — без ръчна намеса. Protected paths.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">⚙️</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Настройки</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">App name, SMTP конфигурация с test, maintenance mode, broadcast banner, език по подразбиране — всичко от UI.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🌍</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Многоезичност</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">lang/en + lang/bg. Admin UI за добавяне на локали, inline редакция на всеки ключ, import/export PHP файлове.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">📄</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">CMS Страници</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Управлявай публичните страници от администрацията. Title, slug, HTML content, SEO meta, навигационен ред.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🧩</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">38+ UI Компонента</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Button, Card, Badge, Modal, Drawer, Toast, Table, Pagination, Tabs, Accordion, Input, Select и още — всичко shadcn/ui parity.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🖥️</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Session Management</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">Виж всички активни сесии с IP и browser. Logout other devices с потвърждение на парола. Database sessions.</p>
  </div>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;">
    <div style="font-size:1.5rem;margin-bottom:.5rem;">🔐</div>
    <h3 style="margin:0 0 .4rem;font-size:1rem;font-weight:700;color:#111827;">Лицензиране</h3>
    <p style="margin:0;font-size:.875rem;color:#6b7280;line-height:1.6;">HMAC-based license validation срещу license server. Weekly ping, domain lock, DEV bypass, license warning banner.</p>
  </div>
</div>

<hr>

<h2>Сигурност на enterprise ниво</h2>
<p style="color:#6b7280;margin-bottom:1rem;">Всеки route е защитен. Rate limiting на всички auth endpoints. Verified email middleware. Double permission check — route + controller.</p>
<ul>
  <li>Rate limiting: login 5/мин, register 3/мин, forgot-password 3/мин</li>
  <li>Email верификация с signed URLs (MustVerifyEmail)</li>
  <li>Suspended потребители блокирани преди session creation</li>
  <li>Session regeneration при login и privilege change</li>
  <li>CSRF защита на всички forms и API endpoints</li>
  <li>Avatar upload: MIME validation + GD re-encode (no payload bypass)</li>
</ul>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin:1.5rem 0;">
  <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:1rem;text-align:center;">
    <div style="font-size:1.75rem;font-weight:800;color:#16a34a;">414</div>
    <div style="font-size:.75rem;color:#6b7280;margin-top:.25rem;">Passing Tests</div>
  </div>
  <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:1rem;text-align:center;">
    <div style="font-size:1.75rem;font-weight:800;color:#16a34a;">0</div>
    <div style="font-size:.75rem;color:#6b7280;margin-top:.25rem;">Known CVEs</div>
  </div>
  <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:1rem;text-align:center;">
    <div style="font-size:1.75rem;font-weight:800;color:#d97706;">2FA</div>
    <div style="font-size:.75rem;color:#6b7280;margin-top:.25rem;">Очаква се</div>
  </div>
  <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:1rem;text-align:center;">
    <div style="font-size:1.75rem;font-weight:800;color:#2563eb;">100%</div>
    <div style="font-size:.75rem;color:#6b7280;margin-top:.25rem;">Route Protected</div>
  </div>
</div>

<hr>

<h2>Модерен технологичен стек</h2>
<p style="color:#6b7280;margin-bottom:1rem;">Само утвърдени, production-ready технологии. Никакви експериментални зависимости.</p>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:1rem;margin:1.5rem 0;">
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">Laravel 13</div>
    <div style="font-size:.75rem;color:#9ca3af;">PHP Framework</div>
  </div>
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">PHP 8.3</div>
    <div style="font-size:.75rem;color:#9ca3af;">Backend</div>
  </div>
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">Tailwind v4</div>
    <div style="font-size:.75rem;color:#9ca3af;">CSS</div>
  </div>
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">Alpine.js v3</div>
    <div style="font-size:.75rem;color:#9ca3af;">Reactivity</div>
  </div>
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">Sanctum</div>
    <div style="font-size:.75rem;color:#9ca3af;">API Auth</div>
  </div>
  <div style="border:1px solid #e5e7eb;border-radius:12px;padding:1.25rem;text-align:center;background:#fff;">
    <div style="font-weight:700;color:#111827;margin-bottom:.25rem;">Spatie</div>
    <div style="font-size:.75rem;color:#9ca3af;">Роли &amp; Логове</div>
  </div>
</div>

<hr>

<h2>Още функции</h2>
<ul>
  <li><strong>Глобално търсене</strong> — Търсене в реално време — потребители, роли, настройки, activity log. 3+ символа → резултати.</li>
  <li><strong>Broadcast Banner</strong> — Site-wide съобщения до всички потребители. Dismissable per session. Управлявай от Settings.</li>
  <li><strong>Dark / Light Mode</strong> — Запазено в localStorage. Без flash при зареждане. Работи на всеки компонент.</li>
  <li><strong>Installer Wizard</strong> — Multi-step install: DB, admin акаунт, license, settings. Self-locks след инсталация.</li>
</ul>

<hr>

<p style="font-size:1rem;color:#374151;text-align:center;padding:1rem 0;">Готови ли сте? Инсталирайте, настройте и публикувайте. Без месеци boilerplate — само вашият продукт.</p>
HTML;

        // Seed EN translations
        $translations = [
            'home' => [
                'en' => [
                    'title'            => 'Home',
                    'excerpt'          => 'Welcome to our platform — the fastest way to launch your SaaS.',
                    'hero_title'       => 'Build Something Amazing',
                    'hero_subtitle'    => 'The modern SaaS starter kit for Laravel developers. Ship faster, scale smarter.',
                    'hero_cta_label'   => 'Get Started Free',
                    'meta_title'       => 'Home',
                    'meta_description' => 'Launch your SaaS product faster with Dravion — the Laravel SaaS starter kit.',
                    'content'          => $homeContentEn,
                ],
                'bg' => [
                    'title'            => 'Начало',
                    'excerpt'          => 'Добре дошли в нашата платформа — най-бързият начин да стартирате вашия SaaS.',
                    'hero_title'       => 'Създайте Нещо Страхотно',
                    'hero_subtitle'    => 'Модерен Laravel SaaS стартов комплект. Пускайте по-бързо, мащабирайте по-умно.',
                    'hero_cta_label'   => 'Започнете безплатно',
                    'meta_title'       => 'Начало',
                    'meta_description' => 'Стартирайте своя SaaS продукт по-бързо с Dravion — Laravel SaaS стартов комплект.',
                    'content'          => $homeContentBg,
                ],
            ],
            'gallery' => [
                'en' => [
                    'title'            => 'Gallery',
                    'excerpt'          => 'Explore our UI components and admin panel features.',
                    'hero_title'       => 'Component Gallery',
                    'hero_subtitle'    => 'A showcase of the built-in UI components, admin panels, and features ready to use in your project.',
                    'hero_cta_label'   => 'View Components',
                    'meta_title'       => 'Gallery',
                    'meta_description' => 'Browse the Dravion UI component library — buttons, badges, alerts, tables, forms, and more.',
                    'content'          => '<h2>UI Component Library</h2><p>Dravion ships with 38+ production-ready UI components built on Tailwind CSS v4 and Alpine.js v3. Every component supports dark mode out of the box.</p><h3>Available Components</h3><ul><li><strong>Buttons</strong> — Primary, Secondary, Danger, Ghost, with size variants</li><li><strong>Badges</strong> — Status indicators with color variants</li><li><strong>Alerts</strong> — Success, Warning, Error inline alerts</li><li><strong>Modals &amp; Drawers</strong> — Accessible dialogs with Alpine.js</li><li><strong>Tables</strong> — Sortable, searchable, paginated data tables</li><li><strong>Forms</strong> — Input, Select, Textarea, Checkbox, Radio, Date Picker</li><li><strong>Cards</strong> — Content containers with header/body/footer slots</li><li><strong>Tabs</strong> — Horizontal and vertical tab navigation</li><li><strong>Accordion</strong> — Collapsible content sections</li><li><strong>Toast Notifications</strong> — Success/error/warning flash messages</li><li><strong>Pagination</strong> — Laravel paginator with query string preservation</li><li><strong>Breadcrumbs</strong> — Navigation breadcrumb component</li><li><strong>Avatar</strong> — User avatar with image or letter fallback</li><li><strong>Spinner &amp; Skeleton</strong> — Loading state indicators</li></ul><hr><h2>Admin Panel Features</h2><p>The admin panel is fully built and ready to customize. It includes a responsive sidebar, dark mode, global search, notification bell, user management, roles &amp; permissions, activity log, settings, and more.</p><h3>Pages &amp; CMS</h3><p>All public pages are managed through the CMS. Create, edit, and delete pages with per-language content, hero sections, SEO meta tags, and rich text editing with TipTap.</p>',
                ],
                'bg' => [
                    'title'            => 'Галерия',
                    'excerpt'          => 'Разгледайте UI компонентите и функциите на администрацията.',
                    'hero_title'       => 'Галерия с компоненти',
                    'hero_subtitle'    => 'Преглед на вградените UI компоненти, администраторски панели и функции, готови за употреба.',
                    'hero_cta_label'   => 'Виж компонентите',
                    'meta_title'       => 'Галерия',
                    'meta_description' => 'Разгледайте библиотеката от UI компоненти на Dravion — бутони, значки, предупреждения, таблици, форми и още.',
                    'content'          => '<h2>Библиотека с UI компоненти</h2><p>Dravion включва 38+ готови за продукция UI компонента, изградени на Tailwind CSS v4 и Alpine.js v3. Всеки компонент поддържа тъмен режим по подразбиране.</p><h3>Налични компоненти</h3><ul><li><strong>Бутони</strong> — Primary, Secondary, Danger, Ghost с размерови варианти</li><li><strong>Значки</strong> — Цветни индикатори за статус</li><li><strong>Предупреждения</strong> — Success, Warning, Error инлайн съобщения</li><li><strong>Модали и чекмеджета</strong> — Достъпни диалози с Alpine.js</li><li><strong>Таблици</strong> — Сортируеми, търсими, пагинирани таблици с данни</li><li><strong>Форми</strong> — Input, Select, Textarea, Checkbox, Radio, Date Picker</li><li><strong>Карти</strong> — Контейнери за съдържание с header/body/footer slots</li><li><strong>Табове</strong> — Хоризонтална и вертикална навигация</li><li><strong>Акордеон</strong> — Сгъваеми секции с съдържание</li><li><strong>Toast нотификации</strong> — Success/error/warning flash съобщения</li><li><strong>Пагинация</strong> — Laravel пагинатор с запазване на query string</li><li><strong>Breadcrumbs</strong> — Навигационен компонент</li><li><strong>Аватар</strong> — Потребителски аватар с image или буква</li><li><strong>Spinner и Skeleton</strong> — Индикатори за зареждане</li></ul><hr><h2>Функции на администрацията</h2><p>Администраторският панел е напълно изграден и готов за персонализиране. Включва responsive sidebar, тъмен режим, глобално търсене, нотификационна камбана, управление на потребители, роли и права, activity log, настройки и много повече.</p><h3>Страници и CMS</h3><p>Всички публични страници се управляват чрез CMS. Създавайте, редактирайте и изтривайте страници с per-language съдържание, hero секции, SEO мета тагове и rich text редактиране с TipTap.</p>',
                ],
            ],
            'contact' => [
                'en' => [
                    'title'            => 'Contact',
                    'excerpt'          => 'Get in touch with our team.',
                    'hero_title'       => 'Get In Touch',
                    'hero_subtitle'    => 'Have a question or want to work together? We are here to help.',
                    'hero_cta_label'   => 'Send Message',
                    'meta_title'       => 'Contact Us',
                    'meta_description' => 'Contact the Dravion team. We are happy to answer your questions.',
                    'content'          => '<h2>Contact Us</h2><p>We would love to hear from you. Fill out the form and we will get back to you within 24 hours.</p><h3>Other ways to reach us</h3><ul><li>Email: hello@example.com</li><li>Phone: +1 (555) 000-0000</li><li>Hours: Monday–Friday, 9am–6pm</li></ul>',
                ],
                'bg' => [
                    'title'            => 'Контакти',
                    'excerpt'          => 'Свържете се с нашия екип.',
                    'hero_title'       => 'Свържете се с нас',
                    'hero_subtitle'    => 'Имате въпрос или искате да работим заедно? Тук сме, за да помогнем.',
                    'hero_cta_label'   => 'Изпратете съобщение',
                    'meta_title'       => 'Контакти',
                    'meta_description' => 'Свържете се с екипа на Dravion. С удоволствие ще отговорим на вашите въпроси.',
                    'content'          => '<h2>Свържете се с нас</h2><p>Радваме се да чуем от вас. Попълнете формата и ще се свържем с вас в рамките на 24 часа.</p><h3>Други начини да се свържете с нас</h3><ul><li>Имейл: hello@example.com</li><li>Телефон: +1 (555) 000-0000</li><li>Работно време: Понеделник–Петък, 9:00–18:00</li></ul>',
                ],
            ],
        ];

        foreach ($translations as $slug => $locales) {
            $page = Page::where('slug', $slug)->first();
            if (! $page) continue;
            foreach ($locales as $locale => $fields) {
                PageTranslation::updateOrCreate(
                    ['page_id' => $page->id, 'locale' => $locale],
                    $fields
                );
            }
        }
    }
}
