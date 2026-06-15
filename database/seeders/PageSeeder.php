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

        // Seed EN translations
        $translations = [
            'home' => [
                'en' => [
                    'title'       => 'Home',
                    'excerpt'     => 'Welcome to our platform — the fastest way to launch your SaaS.',
                    'hero_title'  => 'Build Something Amazing',
                    'hero_subtitle' => 'The modern SaaS starter kit for Laravel developers. Ship faster, scale smarter.',
                    'hero_cta_label' => 'Get Started Free',
                    'meta_title'  => 'Home',
                    'meta_description' => 'Launch your SaaS product faster with Dravion — the Laravel SaaS starter kit.',
                    'content'     => '<h2>Welcome to Dravion</h2><p>Dravion is a production-ready SaaS starter kit built on Laravel 13. It includes everything you need: authentication, roles &amp; permissions, settings, multi-language support, a CMS, and much more.</p><h3>Why Dravion?</h3><ul><li>Built with Laravel 13 and PHP 8.3</li><li>Tailwind CSS v4 + Alpine.js v3</li><li>Role-based access control (admin, manager, editor, user)</li><li>Multi-language admin &amp; public site</li><li>CMS with rich-text editor</li><li>Self-updater via GitHub releases</li></ul><p>Get started today and launch your product in days, not months.</p>',
                ],
                'bg' => [
                    'title'       => 'Начало',
                    'excerpt'     => 'Добре дошли в нашата платформа — най-бързият начин да стартирате вашия SaaS.',
                    'hero_title'  => 'Създайте Нещо Страхотно',
                    'hero_subtitle' => 'Модерен Laravel SaaS стартов комплект. Пускайте по-бързо, мащабирайте по-умно.',
                    'hero_cta_label' => 'Започнете безплатно',
                    'meta_title'  => 'Начало',
                    'meta_description' => 'Стартирайте своя SaaS продукт по-бързо с Dravion — Laravel SaaS стартов комплект.',
                    'content'     => '<h2>Добре дошли в Dravion</h2><p>Dravion е готов за производство SaaS стартов комплект, изграден на Laravel 13. Включва всичко необходимо: автентикация, роли и права, настройки, многоезична поддръжка, CMS и много повече.</p><h3>Защо Dravion?</h3><ul><li>Изграден с Laravel 13 и PHP 8.3</li><li>Tailwind CSS v4 + Alpine.js v3</li><li>Контрол на достъпа по роли (admin, manager, editor, user)</li><li>Многоезична администрация и публичен сайт</li><li>CMS с rich-text редактор</li><li>Самообновяване чрез GitHub releases</li></ul><p>Започнете днес и пуснете продукта си за дни, не месеци.</p>',
                ],
            ],
            'contact' => [
                'en' => [
                    'title'       => 'Contact',
                    'excerpt'     => 'Get in touch with our team.',
                    'hero_title'  => 'Get In Touch',
                    'hero_subtitle' => 'Have a question or want to work together? We are here to help.',
                    'hero_cta_label' => 'Send Message',
                    'meta_title'  => 'Contact Us',
                    'meta_description' => 'Contact the Dravion team. We are happy to answer your questions.',
                    'content'     => '<h2>Contact Us</h2><p>We would love to hear from you. Fill out the form and we will get back to you within 24 hours.</p><h3>Other ways to reach us</h3><ul><li>Email: hello@example.com</li><li>Phone: +1 (555) 000-0000</li><li>Hours: Monday–Friday, 9am–6pm</li></ul>',
                ],
                'bg' => [
                    'title'       => 'Контакти',
                    'excerpt'     => 'Свържете се с нашия екип.',
                    'hero_title'  => 'Свържете се с нас',
                    'hero_subtitle' => 'Имате въпрос или искате да работим заедно? Тук сме, за да помогнем.',
                    'hero_cta_label' => 'Изпратете съобщение',
                    'meta_title'  => 'Контакти',
                    'meta_description' => 'Свържете се с екипа на Dravion. С удоволствие ще отговорим на вашите въпроси.',
                    'content'     => '<h2>Свържете се с нас</h2><p>Радваме се да чуем от вас. Попълнете формата и ще се свържем с вас в рамките на 24 часа.</p><h3>Други начини да се свържете с нас</h3><ul><li>Имейл: hello@example.com</li><li>Телефон: +1 (555) 000-0000</li><li>Работно време: Понеделник–Петък, 9:00–18:00</li></ul>',
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