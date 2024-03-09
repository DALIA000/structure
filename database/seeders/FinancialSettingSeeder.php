<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinancialSetting;

class FinancialSettingSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 1 course-profit-margin
            [
                'model' => [
                    'slug' => 'course-profit-margin',
                    'value' => 0,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'course profit margin',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'قيمة الربح من الدورات',
                    ],
                ],
            ],
            
            // 2 zoom-minute-cost
            [
                'model' => [
                    'slug' => 'zoom-minute-cost',
                    'value' => 0,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'zoom minute course',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'سعر الدقيقة في زووم',
                    ],
                ],
            ],
        ];

        foreach ($data as $item) {
            $model = FinancialSetting::create($item['model']);

            foreach ($item['locales'] as $locale) {
                $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }
        }
    }
}
