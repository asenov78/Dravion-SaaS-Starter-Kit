<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        return [
            'entity'     => 'users',
            'key'        => Str::snake($name) . '_' . uniqid(),
            'name_en'    => ucfirst($name),
            'name_bg'    => ucfirst($name),
            'is_system'  => false,
            'sort_order' => $this->faker->numberBetween(10, 100),
        ];
    }
}
