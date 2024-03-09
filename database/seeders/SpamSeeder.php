<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Spam;

class SpamSeeder extends Seeder
{
    
    public function run()
    {
        $data = [
            // account 1
            /* [
                'model' => [
                    'spam_section_id' => 1,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'fake',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مزيف',
                    ],
                ],
            ], */

            // video 2
            [
                'model' => [
                    'spam_section_id' => 2,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'offenssive',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مهين',
                    ],
                ],
            ],

            // comment 3
            [
                'model' => [
                    'spam_section_id' => 3,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'racist',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عنصري',
                    ],
                ],
            ],

            // live 4
            [
                'model' => [
                    'spam_section_id' => 4,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'broke',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'لا يعمل',
                    ],
                ],
            ],

            // message 5
            [
                'model' => [
                    'spam_section_id' => 5,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'offenssive',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مهين',
                    ],
                ],
            ],
        ];

        foreach ($data as $item) {
            $model = Spam::create($item['model']);

            foreach ($item['locales'] as $locale) {
                $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }
        }
    }
}
