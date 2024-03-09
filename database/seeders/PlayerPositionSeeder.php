<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PlayerPosition;

class PlayerPositionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $data = [
      // goal keeper
      [
        'model' => [
        ],
        'locales' => [
          [
            'locale' => 'en',
            'name' => 'goal keeper',
          ],
          [
            'locale' => 'ar',
            'name' => 'حارس مرمى',
          ]
        ]
      ],
      
      // right back
      [
        'model' => [
        ],
        'locales' => [
          [
            'locale' => 'en',
            'name' => 'right back',
          ],
          [
            'locale' => 'ar',
            'name' => 'يمين خلف',
          ]
        ]
      ],
      
      // left back
      [
        'model' => [
        ],
        'locales' => [
          [
            'locale' => 'en',
            'name' => 'left back',
          ],
          [
            'locale' => 'ar',
            'name' => 'يسار خلف',
          ]
        ]
      ],

      // centre back
      [
        'model' => [
        ],
        'locales' => [
          [
            'locale' => 'en',
            'name' => 'centre back',
          ],
          [
            'locale' => 'ar',
            'name' => 'وسط خلف',
          ]
        ]
      ],

      // central defensive
      [
        'model' => [
        ],
        'locales' => [
          [
            'locale' => 'en',
            'name' => 'central defensive',
          ],
          [
            'locale' => 'ar',
            'name' => 'مهاجم وسط',
          ]
        ]
      ],
    ];

    foreach ($data as $item) {
      $model = PlayerPosition::create($item['model']);

      foreach ($item['locales'] as $locale) {
        $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
      }
    }
  }
}
