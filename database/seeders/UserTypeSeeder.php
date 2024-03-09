<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserType;

class UserTypeSeeder extends Seeder
{
    public function run()
    {
        /** 
         * [
         *      1 => 'account',
         *      2 => 'tags-section',
         *      3 => 'comments', 
         *      4 => 'messages',
         *      5 => 'email-notifications',
         *      6 => 'push-notifications',
         *      7 => 'players-info-section',
         *      8 => 'competitions-section',
         *      9 => 'courses-section',
         *      10 => 'home'
         * ];
        */
        $data = [
            // fan 1
            [
                'model' => [
                    'slug' => 'fan',
                    'user_type' => \App\Models\Fan::class,
                    'available' => 1,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'fan',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مشجع',
                    ]
                ],
                'preferences' => [1, 2, 4, 5, 6, 10], 
            ],

            // federation 2
            [
                'model' => [
                    'slug' => 'federation',
                    'user_type' => \App\Models\Federation::class,
                    'available' => 0,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'federation',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'هيئة حكومية',
                    ]
                ],
                'preferences' => [4, 5, 6, 10], 
            ],

            // club 3
            [
                'model' => [
                    'slug' => 'club',
                    'user_type' => \App\Models\Club::class,
                    'available' => 0,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'club',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'نادي',
                    ]
                ],
                'preferences' => [4, 5, 6,7, 8, 10], 
            ],

            // academy 4
            [
                'model' => [
                    'slug' => 'academy',
                    'user_type' => \App\Models\Academy::class,
                    'available' => 1,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'academy',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'أكاديمية',
                    ]
                ],
                'preferences' => [2, 4, 5, 6, 7, 10], 
            ],

            // player 5
            [
                'model' => [
                    'slug' => 'player',
                    'user_type' => \App\Models\Player::class,
                    'available' => 1,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'young player',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'لاعب ناشئ',
                    ]
                ],
                'preferences' => [1, 2, 4, 5, 6, 10], 
            ],

            // trainer 6
            [
                'model' => [
                    'slug' => 'trainer',
                    'user_type' => \App\Models\Trainer::class,
                    'available' => 1,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'trainer',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'خبير / متخصص',
                    ]
                ],
                'preferences' => [2, 4, 5, 6, 9, 10], 
            ],

            // influencer 7
            [
                'model' => [
                    'slug' => 'influencer',
                    'user_type' => \App\Models\Influencer::class,
                    'available' => 0,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'influencer',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مشهور',
                    ]
                ],
                'preferences' => [2, 4, 5, 6, 10], 
            ],

            // journalist 8
            [
                'model' => [
                    'slug' => 'journalist',
                    'user_type' => \App\Models\Journalist::class,
                    'available' => 1,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'journalist',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'صحفي',
                    ]
                ],
                'preferences' => [2, 4, 5, 6, 10], 
            ],

            // business 9
            [
                'model' => [
                    'slug' => 'business',
                    'user_type' => \App\Models\Business::class,
                    'available' => 1,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'business',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تجاري',
                    ]
                ],
                'preferences' => [2, 4, 5, 6, 10], 
            ],
        ];

        foreach ($data as $item) {
            $model = UserType::create($item['model']);

            foreach ($item['locales'] as $locale) {
                $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }

            foreach ($item['preferences'] as $preference) {
                \App\Models\UserTypePreference::create([
                    'user_type_class' => $model->user_type,
                    'preference_id' => $preference,
                ]);
            }
        }
    }
}
