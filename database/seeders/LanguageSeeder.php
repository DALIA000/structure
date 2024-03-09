<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      [
        'model' => [
            'slug' => 'en',
            'direction' => 'ltr'
        ],
        'locales' => [
          [
            'locale' => 'en',
            'name' => 'english',
          ],
          [
            'locale' => 'ar',
            'name' => 'english',
          ]
        ]
      ],
      
      [
        'model' => [
            'slug' => 'ar',
            'direction' => 'rtl'
        ],
        'locales' => [
          [
            'locale' => 'ar',
            'name' => 'arabic',
          ],
          [
            'locale' => 'ar',
            'name' => 'arabic',
          ]
        ]
      ],
    ];

    foreach ($data as $item) {
      $model = Language::create($item['model']);

      foreach ($item['locales'] as $locale) {
        $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
      }
    }
  }
}
