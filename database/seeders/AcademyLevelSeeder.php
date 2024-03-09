<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademyLevel;

class AcademyLevelSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      // high
      [
        'model' => [
        ],
        'locales' => [
          [
            'locale' => 'en',
            'name' => 'high',
          ],
          [
            'locale' => 'ar',
            'name' => 'عالي',
          ]
        ]
      ],
      
      // medium
      [
        'model' => [
        ],
        'locales' => [
          [
            'locale' => 'en',
            'name' => 'medium',
          ],
          [
            'locale' => 'ar',
            'name' => 'متوسط',
          ]
        ]
      ],
    ];

    foreach ($data as $item) {
      $model = AcademyLevel::create($item['model']);

      foreach ($item['locales'] as $locale) {
        $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
      }
    }
  }
}
