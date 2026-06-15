<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function translate(string $locale): object
    {
        $t = $this->translations->firstWhere('locale', $locale);
        return (object) [
            'title'            => $t?->title            ?? $this->title,
            'content'          => $t?->content           ?? $this->content,
            'excerpt'          => $t?->excerpt           ?? $this->excerpt,
            'hero_title'       => $t?->hero_title        ?? $this->hero_title,
            'hero_subtitle'    => $t?->hero_subtitle     ?? $this->hero_subtitle,
            'hero_cta_label'   => $t?->hero_cta_label    ?? $this->hero_cta_label,
            'meta_title'       => $t?->meta_title        ?? $this->meta_title,
            'meta_description' => $t?->meta_description  ?? $this->meta_description,
        ];
    }

    public function scopePublished($query) {
        return $query->where('is_published', true);
    }

    public function scopeInNav($query) {
        return $query->where('show_in_nav', true)->orderBy('sort_order');
    }
}