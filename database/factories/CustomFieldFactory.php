<?php

namespace Database\Factories;

use App\Models\CustomCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomFieldFactory extends Factory
{
    public function definition(): array
    {
        $label = $this->faker->unique()->words(2, true);
        return [
            'category_id' => CustomCategory::factory(),
            'key'         => Str::snake($label) . '_' . uniqid(),
            'label_en'    => ucfirst($label),
            'label_bg'    => ucfirst($label),
            'type'        => 'text',
            'options'     => null,
            'is_required' => false,
            'is_visible'  => true,
            'is_system'   => false,
            'sort_order'  => $this->faker->numberBetween(10, 100),
        ];
    }
}
