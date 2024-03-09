<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Model;

class ModelSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // admin
            [
                'model' => [
                    'slug' => 'admin',
                    'table_name' => 'admins',
                    'type' => \App\Models\Admin::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'admin',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مدير',
                    ]
                ],
                'status_options' => [1],
            ],

            // country
            [
                'model' => [
                    'slug' => 'country',
                    'table_name' => 'countries',
                    'type' => \App\Models\Country::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'country',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'دولة',
                    ]
                ],
                'status_options' => [1],
            ],

            // city
            [
                'model' => [
                    'slug' => 'city',
                    'table_name' => 'cities',
                    'type' => \App\Models\City::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'city',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'دولة',
                    ]
                ],
                'status_options' => [1],
            ],

            // user
            [
                'model' => [
                    'slug' => 'user',
                    'table_name' => 'users',
                    'type' => \App\Models\User::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'user',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مستخدم',
                    ]
                ],
                'status_options' => [],
            ],

            // fan
            [
                'model' => [
                    'slug' => 'fan',
                    'table_name' => 'fans',
                    'type' => \App\Models\Fan::class,
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
                'status_options' => [1, 4],
            ],

            // academy
            [
                'model' => [
                    'slug' => 'academy',
                    'table_name' => 'academies',
                    'type' => \App\Models\Academy::class,
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
                'status_options' => [1, 2, 3, 4, 5],
            ],

            // player
            [
                'model' => [
                    'slug' => 'player',
                    'table_name' => 'players',
                    'type' => \App\Models\Player::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'player',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'لاعب ناشئ',
                    ]
                ],
                'status_options' => [1, 2, 3, 4, 5],
            ],

            // trainer
            [
                'model' => [
                    'slug' => 'trainer',
                    'table_name' => 'trainers',
                    'type' => \App\Models\Trainer::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'trainer',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مدرب',
                    ]
                ],
                'status_options' => [1, 2, 3, 4, 5],
            ],

            // club
            [
                'model' => [
                    'slug' => 'club',
                    'table_name' => 'clubs',
                    'type' => \App\Models\Club::class,
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
                'status_options' => [1, 4],
            ],

            // federation
            [
                'model' => [
                    'slug' => 'federation',
                    'table_name' => 'federations',
                    'type' => \App\Models\Federation::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'federation',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'هيئة',
                    ]
                ],
                'status_options' => [1, 4],
            ],

            // business
            [
                'model' => [
                    'slug' => 'business',
                    'table_name' => 'businesses',
                    'type' => \App\Models\Business::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'business',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'هيئة',
                    ]
                ],
                'status_options' => [1, 2, 3, 4, 5],
            ],

            // journalist
            [
                'model' => [
                    'slug' => 'journalist',
                    'table_name' => 'journalists',
                    'type' => \App\Models\Journalist::class,
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
                'status_options' => [1, 2, 3, 4, 5],
            ],

            // influencer
            [
                'model' => [
                    'slug' => 'influencer',
                    'table_name' => 'influencers',
                    'type' => \App\Models\Influencers::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'influencer',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'انفلونسر',
                    ]
                ],
                'status_options' => [1, 2, 3, 4, 5],
            ],

            // video
            [
                'model' => [
                    'slug' => 'video',
                    'table_name' => 'videos',
                    'type' => \App\Models\Video::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'video',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'فيديو',
                    ]
                ],
                'status_options' => [1, 7],
            ],

            // like
            [
                'model' => [
                    'slug' => 'like',
                    'table_name' => 'likes',
                    'type' => \App\Models\Like::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'like',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'إعجاب',
                    ]
                ],
                'status_options' => [],
            ],

            // comment
            [
                'model' => [
                    'slug' => 'comment',
                    'table_name' => 'comments',
                    'type' => \App\Models\Comment::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'comment',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعليق',
                    ]
                ],
                'status_options' => [1, 7],
            ],

            // view
            [
                'model' => [
                    'slug' => 'view',
                    'table_name' => 'views',
                    'type' => \App\Models\View::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                    ]
                ],
                'status_options' => [1],
            ],

            // share
            [
                'model' => [
                    'slug' => 'share',
                    'table_name' => 'shares',
                    'type' => \App\Models\Share::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'share',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مشاركة',
                    ]
                ],
                'status_options' => [1],
            ],

            // blog
            [
                'model' => [
                    'slug' => 'blog',
                    'table_name' => 'blogs',
                    'type' => \App\Models\Blog::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'blog',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مقالة',
                    ]
                ],
                'status_options' => [1,6],
            ],

            // course
            [
                'model' => [
                    'slug' => 'course',
                    'table_name' => 'courses',
                    'type' => \App\Models\Course::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'course',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مسار',
                    ]
                ],
                'status_options' => [1, 2, 3, 5],
            ],

            // hashtag
            [
                'model' => [
                    'slug' => 'hashtag',
                    'table_name' => 'hashtags',
                    'type' => \App\Models\Hashtag::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'hashtag',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'هاشتاج',
                    ]
                ],
                'status_options' => [],
            ],

            // message
            [
                'model' => [
                    'slug' => 'message',
                    'table_name' => 'messages',
                    'type' => \Musonza\Chat\Models\Message::class,
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'message',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'رسالة',
                    ]
                ],
                'status_options' => [],
            ],
        ];

        foreach ($data as $item) {
            $model = Model::create($item['model']);

            foreach ($item['locales'] as $locale) {
                $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }

            foreach ($item['status_options'] as $status_option) {
                \App\Models\ModelStatus::create([
                    'model_id' => $model->id,
                    'status_id' => $status_option,
                ]);
            }

        }
    }
}
