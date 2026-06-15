<?php
namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageHeroSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'home' => [
                'hero_image'     => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1920&q=80',
                'hero_title'     => 'Пълноценен SaaS Starter Kit',
                'hero_subtitle'  => 'Спестете месеци разработка. Ролева система, лицензиране, само-обновяване, in-app нотификации, CMS и десетки готови компоненти — всичко в едно.',
                'hero_cta_label' => 'Стартирай безплатно',
                'hero_cta_url'   => '/register',
            ],
            'contact' => [
                'hero_image'    => 'https://images.unsplash.com/photo-1516387938699-a93567ec168e?auto=format&fit=crop&w=1920&q=80',
                'hero_title'    => null,
                'hero_subtitle' => null,
            ],
            'gallery' => [
                'hero_image'    => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1920&q=80',
                'hero_title'    => null,
                'hero_subtitle' => null,
            ],
        ];

        foreach ($defaults as $slug => $fields) {
            Page::where('slug', $slug)->update(array_filter($fields, fn($v) => $v !== null));
        }
    }
}