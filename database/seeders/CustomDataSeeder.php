<?php

namespace Database\Seeders;

use App\Models\CustomCategory;
use App\Models\CustomField;
use Illuminate\Database\Seeder;

class CustomDataSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'entity'     => 'users',
                'key'        => 'account',
                'name_en'    => 'Account',
                'name_bg'    => 'Акаунт',
                'is_system'  => true,
                'sort_order' => 10,
                'fields'     => [],
            ],
            [
                'entity'     => 'users',
                'key'        => 'personal_info',
                'name_en'    => 'Personal Information',
                'name_bg'    => 'Лична информация',
                'is_system'  => true,
                'sort_order' => 20,
                'fields'     => [
                    [
                        'key'        => 'phone',
                        'label_en'   => 'Phone',
                        'label_bg'   => 'Телефон',
                        'type'       => 'text',
                        'is_system'  => true,
                        'is_visible' => true,
                        'is_required'=> false,
                        'sort_order' => 10,
                    ],
                ],
            ],
            [
                'entity'     => 'users',
                'key'        => 'address',
                'name_en'    => 'Address',
                'name_bg'    => 'Адрес',
                'is_system'  => true,
                'sort_order' => 30,
                'fields'     => [
                    [
                        'key'        => 'country',
                        'label_en'   => 'Country',
                        'label_bg'   => 'Държава',
                        'type'       => 'text',
                        'is_system'  => true,
                        'is_visible' => true,
                        'is_required'=> false,
                        'sort_order' => 10,
                    ],
                    [
                        'key'        => 'city_state',
                        'label_en'   => 'City / State',
                        'label_bg'   => 'Град / Щат',
                        'type'       => 'text',
                        'is_system'  => true,
                        'is_visible' => true,
                        'is_required'=> false,
                        'sort_order' => 20,
                    ],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $fields = $catData['fields'];
            unset($catData['fields']);

            $cat = CustomCategory::updateOrCreate(['key' => $catData['key']], $catData);

            foreach ($fields as $fieldData) {
                CustomField::updateOrCreate(
                    ['key' => $fieldData['key']],
                    array_merge($fieldData, ['category_id' => $cat->id])
                );
            }
        }
    }
}
