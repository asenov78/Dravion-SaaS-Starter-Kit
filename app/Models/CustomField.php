<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomField extends Model
{
    protected $fillable = [
        'category_id', 'key', 'label_en', 'label_bg',
        'type', 'options', 'is_required', 'is_visible', 'is_system', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options'     => 'array',
            'is_required' => 'boolean',
            'is_visible'  => 'boolean',
            'is_system'   => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CustomCategory::class, 'category_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(UserFieldValue::class, 'field_id');
    }

    public function label(): string
    {
        $locale = app()->getLocale();
        return $locale === 'bg' ? $this->label_bg : $this->label_en;
    }

    public function optionLabel(array $option): string
    {
        $locale = app()->getLocale();
        return $option[$locale] ?? $option['en'] ?? '';
    }

    public function optionValue(array $option): string
    {
        return $option['en'] ?? '';
    }
}
