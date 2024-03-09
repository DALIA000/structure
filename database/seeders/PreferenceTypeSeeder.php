<?php

namespace Database\Seeders;

use App\Models\PreferenceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PreferenceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // general 1
            [
                'model' => [
                    'slug' => 'general',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'general',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عام',
                    ]
                ]
            ],

            // section 2
            [
                'model' => [
                    'slug' => 'section',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'section',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'قسم',
                    ]
                ]
            ],
            
            // comunication 3
            [
                'model' => [
                    'slug' => 'comunication',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'comunication',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تواصل',
                    ]
                ]
            ],
            
            // account 4
            [
                'model' => [
                    'slug' => 'account',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'account',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'الحساب الشخصي',
                    ]
                ]
            ],
        ];

        foreach ($data as $item) {
            $model = PreferenceType::create($item['model']);

            foreach ($item['locales'] as $locale) {
                $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }
        }
    }
}
