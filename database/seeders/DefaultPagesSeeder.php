<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class DefaultPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug'           => 'home',
                'title'          => 'Home',
                'excerpt'        => 'Welcome to our platform.',
                'content'        => '<h2>Welcome</h2><p>This is the home page. Edit it from the admin panel.</p>',
                'is_published'   => true,
                'show_in_nav'    => false,
                'sort_order'     => 0,
                'hero_image'     => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1920&q=80',
                'hero_title'     => 'Build Something Amazing',
                'hero_subtitle'  => 'The modern SaaS starter kit for Laravel developers.',
                'hero_cta_label' => 'Get Started',
                'hero_cta_url'   => '/register',
            ],
            [
                'slug'         => 'about',
                'title'        => 'About',
                'excerpt'      => 'Learn more about us.',
                'content'      => '<h2>About Us</h2><p>We build great software. Edit this page from the admin panel.</p>',
                'is_published' => true,
                'show_in_nav'  => true,
                'sort_order'   => 1,
            ],
            [
                'slug'         => 'pricing',
                'title'        => 'Pricing',
                'excerpt'      => 'Simple, transparent pricing.',
                'content'      => '<h2>Pricing</h2><p>Edit this page from the admin panel.</p>',
                'is_published' => true,
                'show_in_nav'  => true,
                'sort_order'   => 2,
            ],
            [
                'slug'         => 'contact',
                'title'        => 'Contact',
                'excerpt'      => 'Get in touch with us.',
                'content'      => '',
                'is_published' => true,
                'show_in_nav'  => true,
                'sort_order'   => 3,
                'hero_image'   => 'https://images.unsplash.com/photo-1516387938699-a93567ec168e?auto=format&fit=crop&w=1920&q=80',
            ],
            [
                'slug'         => 'gallery',
                'title'        => 'Gallery',
                'excerpt'      => 'Component showcase.',
                'content'      => '',
                'is_published' => true,
                'show_in_nav'  => true,
                'sort_order'   => 4,
                'hero_image'   => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1920&q=80',
            ],
        ];

        foreach ($pages as $data) {
            Page::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
