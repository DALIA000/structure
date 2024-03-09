<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PlayerFootness;

class PlayerFootnessSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      // right
      [
        'model' => [
        ],
        'locales' => [
          [
            'locale' => 'en',
            'name' => 'right',
          ],
          [
            'locale' => 'ar',
            'name' => 'يمين',
          ]
        ]
      ],
      
      // left
      [
        'model' => [
        ],
        'locales' => [
          [
            'locale' => 'en',
            'name' => 'left',
          ],
          [
            'locale' => 'ar',
            'name' => 'يسار',
          ]
        ]
      ],
    ];

    foreach ($data as $item) {
      $model = PlayerFootness::create($item['model']);

      foreach ($item['locales'] as $locale) {
        $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
      }
    }
  }
}
