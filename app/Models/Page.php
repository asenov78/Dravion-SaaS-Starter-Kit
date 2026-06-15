<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'excerpt',
        'is_published', 'show_in_nav', 'sort_order',
        'meta_title', 'meta_description',
        'hero_image', 'hero_title', 'hero_subtitle', 'hero_cta_label', 'hero_cta_url',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'show_in_nav'  => 'boolean',
    ];

    public function scopePublished($query) {
        return $query->where('is_published', true);
    }

    public function scopeInNav($query) {
        return $query->where('show_in_nav', true)->orderBy('sort_order');
    }
}