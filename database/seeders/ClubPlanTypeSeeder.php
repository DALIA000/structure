<?php

namespace Database\Seeders;

use App\Models\ClubPlanType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClubPlanTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 1 monthly
            [
                'model' => [
                    'slug' => 'monthly'
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'monthly',
                        'description' => 'monthly',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'شهري',
                        'description' => 'شهري',
                    ],
                ],
            ],

            // biannual
            [
                'model' => [
                    'slug' => 'biannual'
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'biannual',
                        'description' => 'biannual',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'نصف سنوي',
                        'description' => 'نصف سنوي',
                    ],
                ],
            ],

            // 3 annual
            [
                'model' => [
                    'slug' => 'annual'
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'annual',
                        'description' => 'annual',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'سنوي',
                        'description' => 'سنوي',
                    ],
                ],
            ],

        ];

        foreach ($data as $item) {
            $model = ClubPlanType::create($item['model']);

            foreach ($item['locales'] as $locale) {
                $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }
        }
    }
}
