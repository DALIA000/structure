<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SpamSection;

class SpamSectionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // account 1
            /* [
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
                        'name' => ' مستخدم',
                    ],
                ],
            ], */

            // video 2
            [
                'model' => [
                    'id' => 2,
                    'slug' => 'video',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'video',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'فيديو',
                    ],
                ],
            ],

            // comment 3
            [
                'model' => [
                    'id' => 3,
                    'slug' => 'comment',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'comment',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعليق',
                    ],
                ],
            ],

            // live 4
            [
                'model' => [
                    'id' => 4,
                    'slug' => 'live',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'live',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مباشر',
                    ],
                ],
            ],

            // message 5
            [
                'model' => [
                    'id' => 5,
                    'slug' => 'message',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'message',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'رسالة',
                    ],
                ],
            ],
        ];

        foreach ($data as $item) {
            $model = SpamSection::create($item['model']);

            foreach ($item['locales'] as $locale) {
                $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }
        }
    }
}
