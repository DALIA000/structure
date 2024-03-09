<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // 1 active
            [
                'model' => [
                    'slug' => 'active',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'active',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مفعل',
                    ],
                ],
            ],

            // 2 pending
            [
                'model' => [
                    'slug' => 'pending',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'pending',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'قيد الإنتظار',
                    ],
                ],
            ],

            // 3 rejected
            [
                'model' => [
                    'slug' => 'rejected',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'rejected',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مرفوض',
                    ],
                ],
            ],

            // 4 blocked
            [
                'model' => [
                    'slug' => 'blocked',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'blocked',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'محظور',
                    ],
                ],
            ],

            // 5 need action
            [
                'model' => [
                    'slug' => 'need-action',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'need action',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'في انتظار أمر',
                    ],
                ],
            ],

            // 6 archived
            [
                'model' => [
                    'slug' => 'archived',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'archived',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مأرشف',
                    ],
                ],
            ],

            // 7 draft
            [
                'model' => [
                    'slug' => 'draft',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'draft',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مسودة',
                    ],
                ],
            ],

            // 8 allowed
            [
                'model' => [
                    'slug' => 'allowed',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'allowed',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'مسموح',
                    ],
                ],
            ],

            // 9 disallowed
            [
                'model' => [
                    'slug' => 'disallowed',
                ],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'disallowed',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'غير مسموح',
                    ],
                ],
            ],
        ];

        foreach ($data as $item) {
            $model = Status::create($item['model']);

            foreach ($item['locales'] as $locale) {
                $model->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }
        }
    }
}
