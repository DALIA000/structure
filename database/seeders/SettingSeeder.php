<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'slug' => 'terms-and-conditions',
                'description_en' => 'lorem ipsum dolor sit amet ',
                'description_ar' => 'lorem ipsum dolor sit amet',
            ],
            [
                'slug' => 'privacy-policy',
                'description_en' => 'lorem ipsum dolor sit amet',
                'description_ar' => 'lorem ipsum dolor sit amet',
            ],
            [
                'slug' => 'about-us',
                'description_en' => 'lorem ipsum dolor sit amet',
                'description_ar' => 'lorem ipsum dolor sit amet',

            ],
            [
                'slug' => 'trainer-course-conditions',
                'description_en' => 'lorem ipsum dolor sit amet',
                'description_ar' => 'lorem ipsum dolor sit amet',

            ],
            [
                'slug' => 'user-course-conditions',
                'description_en' => 'lorem ipsum dolor sit amet',
                'description_ar' => 'lorem ipsum dolor sit amet',

            ],
        ];

        foreach ($items as $item) {
            $section = Setting::create([
                'slug' => $item['slug'],
            ]);
            $section->locales()->create(
                [
                    'description' => $item['description_ar'] ?? null,
                    'name' => $item['name_ar'] ?? null,
                    'locale' => 'ar',
                ]
            );

            $section->locales()->create(
                [
                    'description' => $item['description_en'] ?? null,
                    'name' => $item['name_en'] ?? null,
                    'locale' => 'en',
                ]
            );
        }
    }
}
