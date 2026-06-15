<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    protected $fillable = [
        'page_id', 'locale', 'title', 'content', 'excerpt',
        'hero_title', 'hero_subtitle', 'hero_cta_label',
        'meta_title', 'meta_description',
    ];
}