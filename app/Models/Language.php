<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $fillable = ['code', 'name', 'flag', 'is_default'];

    protected $casts = ['is_default' => 'boolean'];

    public function lines(): HasMany
    {
        return $this->hasMany(LanguageLine::class);
    }
}
