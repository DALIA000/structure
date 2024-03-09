<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Preference;

class PreferenceSeeder extends Seeder
{
    public function run()
    {
        $data = [
          // account 1
          [
            'model' => [
                'slug' => 'account',
                'type' => 1, // boolean
                'preference_type_id' => 4, // account
                'is_professional_specific' => 0,
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

          // tags section 2
          [
            'model' => [
                'slug' => 'tags-section',
                'type' => 1, // boolean
                'preference_type_id' => 2, // section
                'is_professional_specific' => 0,
            ],
            'locales' => [
              [
                'locale' => 'en',
                'name' => 'tags',
              ],
              [
                'locale' => 'ar',
                'name' => 'تاج',
              ]
            ]
          ],

          // comments 3
          [
            'model' => [
                'slug' => 'comments',
                'type' => 1, // boolean
                'preference_type_id' => 3, // comunication
                'is_professional_specific' => 0,
            ],
            'locales' => [
              [
                'locale' => 'en',
                'name' => 'comments',
              ],
              [
                'locale' => 'ar',
                'name' => 'التعليقات',
              ]
            ]
          ],

          // messages 4
          [
            'model' => [
                'slug' => 'messages',
                'type' => 1, // boolean
                'preference_type_id' => 3, // comunication
                'is_professional_specific' => 0,
            ],
            'locales' => [
              [
                'locale' => 'en',
                'name' => 'messages',
              ],
              [
                'locale' => 'ar',
                'name' => 'الرسائل',
              ]
            ]
          ],

          // email notifications 5
          [
            'model' => [
                'slug' => 'email-notifications',
                'type' => 1, // boolean
                'preference_type_id' => 4, // account
                'is_professional_specific' => 0,
            ],
            'locales' => [
              [
                'locale' => 'en',
                'name' => 'email notifications',
              ],
              [
                'locale' => 'ar',
                'name' => 'تنبيهات البريد الإلكتروني',
              ]
            ]
          ],

          // push notifications 6
          [
            'model' => [
                'slug' => 'push-notifications',
                'type' => 1, // boolean
                'preference_type_id' => 4, // account
                'is_professional_specific' => 0,
            ],
            'locales' => [
              [
                'locale' => 'en',
                'name' => 'push notifications',
              ],
              [
                'locale' => 'ar',
                'name' => 'تنبيهات الموبايل',
              ]
            ]
          ],

          // players info section 7
          [
            'model' => [
                'slug' => 'players-info-section',
                'type' => 1, // boolean
                'preference_type_id' => 2, // section
                'is_professional_specific' => 1,
            ],
            'locales' => [
              [
                'locale' => 'en',
                'name' => 'players',
              ],
              [
                'locale' => 'ar',
                'name' => 'اللاعبين',
              ]
            ]
          ],

          // competitions section 8
          [
            'model' => [
                'slug' => 'competitions-section',
                'type' => 1, // boolean
                'preference_type_id' => 2, // section
                'is_professional_specific' => 1,
            ],
            'locales' => [
              [
                'locale' => 'en',
                'name' => 'competitions',
              ],
              [
                'locale' => 'ar',
                'name' => 'المسابقات',
              ]
            ]
          ],

          // courses section 9
          [
            'model' => [
                'slug' => 'courses-section',
                'type' => 1, // boolean
                'preference_type_id' => 2, // section
                'is_professional_specific' => 1,
            ],
            'locales' => [
              [
                'locale' => 'en',
                'name' => 'courses',
              ],
              [
                'locale' => 'ar',
                'name' => 'الكورسات',
              ]
            ]
          ],

          // home 10
          [
            'model' => [
                'slug' => 'home',
                'type' => 2, // options [1 => videos, 2 => news, 3 => teams]
                'preference_type_id' => 1,
                'is_professional_specific' => 0,
            ],
            'locales' => [
              [
                'locale' => 'en',
                'name' => 'home page',
              ],
              [
                'locale' => 'ar',
                'name' => 'الصفحة الرئيسية',
              ]
            ]
          ],
        ];

        foreach ($data as $item) {
            $model = Preference::create($item['model']);

            foreach ($item['locales'] as $locale) {
                $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }
        }
    }
}
