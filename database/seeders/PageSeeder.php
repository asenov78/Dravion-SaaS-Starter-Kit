<?php
namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['title' => 'Home',    'slug' => 'home',    'show_in_nav' => false, 'sort_order' => 0,
             'excerpt' => 'Welcome to our platform.',
             'content' => '<h2>Welcome</h2><p>This is the home page. Edit it from the admin panel.</p>'],
            ['title' => 'About',   'slug' => 'about',   'show_in_nav' => true,  'sort_order' => 1,
             'excerpt' => 'Learn more about us.',
             'content' => '<h2>About Us</h2><p>We build great software. Edit this page from the admin panel.</p>'],
            ['title' => 'Pricing', 'slug' => 'pricing', 'show_in_nav' => true,  'sort_order' => 2,
             'excerpt' => 'Simple, transparent pricing.',
             'content' => '<h2>Pricing</h2><p>Choose the plan that works for you. Edit from admin panel.</p>'],
            ['title' => 'Contact', 'slug' => 'contact', 'show_in_nav' => true,  'sort_order' => 3,
             'excerpt' => 'Get in touch with us.',
             'content' => '<h2>Contact</h2><p>Reach out to us anytime. Edit this page from the admin panel.</p>'],
        ];

        foreach ($pages as $data) {
            Page::firstOrCreate(['slug' => $data['slug']], array_merge($data, ['is_published' => true]));
        }
    }
}