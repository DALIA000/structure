<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TrainerExperienceLevel;

class TrainerExperienceLevelSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // begginer
            [
                'model' => [
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'begginer',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مبتدأ',
                    ]
                ]
            ],

            // intermediate
            [
                'model' => [
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'intermediate',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'متوسط',
                    ]
                ]
            ],

            // advanced
            [
                'model' => [
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'advanced',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'محترف',
                    ]
                ]
            ],
        ];

        foreach ($data as $item) {
            $model = TrainerExperienceLevel::create($item['model']);

            foreach ($item['locales'] as $locale) {
                $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }
        }
    }
}
