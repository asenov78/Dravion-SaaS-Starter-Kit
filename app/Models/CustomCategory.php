<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomCategory extends Model
{
    protected $fillable = ['entity', 'key', 'name_en', 'name_bg', 'is_system', 'sort_order'];

    protected function casts(): array
    {
        return ['is_system' => 'boolean'];
    }

    public function fields(): HasMany
    {
        return $this->hasMany(CustomField::class, 'category_id')->orderBy('sort_order');
    }

    public function label(): string
    {
        $locale = app()->getLocale();
        return $locale === 'bg' ? $this->name_bg : $this->name_en;
    }
}
