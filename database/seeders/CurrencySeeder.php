<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        $data = [
            // usd
            [
                'model' => [
                    'slug' => 'usd',
                    'value' => 1,
                    'currency_slug' => 'usd',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'USD',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'دولار',
                    ]
                ]
            ],

            // sar
            [
                'model' => [
                    'slug' => 'sar',
                    'value' => 0.27,
                    'currency_slug' => 'usd'
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'SAR',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'رس',
                    ]
                ]
            ],
        ];

        foreach ($data as $item) {
            $model = Currency::create($item['model']);

            foreach ($item['locales'] as $locale) {
                $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }
        }

    }
}
