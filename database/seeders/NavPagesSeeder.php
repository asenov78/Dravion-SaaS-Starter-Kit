<?php
namespace Database\Seeders;
use App\Models\Page;
use Illuminate\Database\Seeder;
class NavPagesSeeder extends Seeder {
    public function run(): void {
        Page::firstOrCreate(['slug'=>'contact'],['title'=>'Contact','excerpt'=>'Get in touch with us.','content'=>'','is_published'=>true,'show_in_nav'=>true,'sort_order'=>4]);
        Page::firstOrCreate(['slug'=>'gallery'],['title'=>'Gallery','excerpt'=>'Component showcase.','content'=>'','is_published'=>true,'show_in_nav'=>true,'sort_order'=>5]);
    }
}