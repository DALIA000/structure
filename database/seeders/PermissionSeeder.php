<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\RoutePermission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // admin
            [
                'name' => 'admin.view',
                'guard_name' => 'admin',
                'model' => 'admin',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'admins',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'مدراء',
                    ]
                ]
            ],
            [
                'name' => 'admin.create',
                'guard_name' => 'admin',
                'model' => 'admin',
                'required_permissions_slugs' => ['admin.view', 'role.view'],
                'locales' => [
                    [
                            'locale' => 'en',
                            'name' => 'create',
                            'group' => 'admins',
                    ],
                    [
                            'locale' => 'ar',
                            'name' => 'اضافة',
                            'group' => 'مدراء',
                    ]
                ]
            ],
            [
                'name' => 'admin.edit',
                'guard_name' => 'admin',
                'model' => 'admin',
                'required_permissions_slugs' => ['admin.view', 'role.view'],
                'locales' => [
                    [
                            'locale' => 'en',
                            'name' => 'edit',
                            'group' => 'admins',
                    ],
                    [
                            'locale' => 'ar',
                            'name' => 'تعديل',
                            'group' => 'مدراء',
                    ]
                ]
            ],
            [
                'name' => 'admin.delete',
                'guard_name' => 'admin',
                'model' => 'admin',
                'required_permissions_slugs' => ['admin.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'admins',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'مدراء',
                        ]
                    ]
            ],

            // role
            [
                'name' => 'role.view',
                'guard_name' => 'admin',
                'model' => 'role',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'roles',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'رتبة',
                    ]
                ]
            ],
            [
                'name' => 'role.create',
                'guard_name' => 'admin',
                'model' => 'role',
                'required_permissions_slugs' => ['role.view', 'permission.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'roles',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'رتبة',
                    ]
                ]
            ],
            [
                'name' => 'role.edit',
                'guard_name' => 'admin',
                'model' => 'role',
                'required_permissions_slugs' => ['role.view', 'permission.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'roles',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'رتبة',
                    ]
                ]
            ],
            [
                'name' => 'role.delete',
                'guard_name' => 'admin',
                'model' => 'role',
                'required_permissions_slugs' => ['role.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'roles',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'رتبة',
                    ]
                ]
            ],

            // currency
            /* [
                'name' => 'currency.view',
                'guard_name' => 'admin',
                'model' => 'currency',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'currencies',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'عملات',
                    ]
                ]
            ], */

            // country
            [
                'name' => 'country.view',
                'guard_name' => 'admin',
                'model' => 'country',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'countries',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'دول',
                    ]
                ]
            ],
            [
                'name' => 'country.create',
                'guard_name' => 'admin',
                'model' => 'country',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'countries',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'اضافة',
                    ]
                ]
            ],
            [
                'name' => 'country.edit',
                'guard_name' => 'admin',
                'model' => 'country',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'countries',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'دول',
                    ]
                ]
            ],
            [
                'name' => 'country.delete',
                'guard_name' => 'admin',
                'model' => 'country',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'countries',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'دول',
                    ]
                ]
            ],

            // city
            [
                'name' => 'city.view',
                'guard_name' => 'admin',
                'model' => 'city',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'cities',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'مدن',
                    ]
                ]
            ],
            [
                'name' => 'city.create',
                'guard_name' => 'admin',
                'model' => 'city',
                'required_permissions_slugs' => ['city.view', 'country.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'cities',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'مدن',
                    ]
                ]
            ],
            [
                'name' => 'city.edit',
                'guard_name' => 'admin',
                'model' => 'city',
                'required_permissions_slugs' => ['city.view', 'country.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'cities',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'مدن',
                    ]
                ]
            ],
            [
                'name' => 'city.delete',
                'guard_name' => 'admin',
                'model' => 'city',
                'required_permissions_slugs' => ['city.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'cities',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'مدن',
                    ]
                ]
            ],

            //setting
            [
                'name' => 'setting.view',
                'guard_name' => 'admin',
                'model' => 'setting',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'setting',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'إعدادات',
                    ]
                ]
            ],
            [
                'name' => 'setting.edit',
                'guard_name' => 'admin',
                'model' => 'setting',
                'required_permissions_slugs' => ['setting.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'setting',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'إعدادات',
                    ]
                ]
            ],

            // spam
            [
                'name' => 'spam.view',
                'guard_name' => 'admin',
                'model' => 'spam',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'spams',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'أسباب التبليغات',
                    ]
                ]
            ],
            [
                'name' => 'spam.create',
                'guard_name' => 'admin',
                'model' => 'spam',
                'required_permissions_slugs' => ['spam.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'spams',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'أسباب التبليغات',
                    ]
                ]
            ],
            [
                'name' => 'spam.edit',
                'guard_name' => 'admin',
                'model' => 'spam',
                'required_permissions_slugs' => ['spam.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'spams',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'أسباب التبليغات',
                    ]
                ]
            ],
            [
                'name' => 'spam.delete',
                'guard_name' => 'admin',
                'model' => 'spam',
                'required_permissions_slugs' => ['spam.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'spams',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'أسباب التبليغات',
                    ]
                ]
            ],

            // academy-level
            [
                'name' => 'academy-level.view',
                'guard_name' => 'admin',
                'model' => 'academy-level',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'academy levels',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'مستويات الأكاديمية',
                    ]
                ]
            ],
            [
                'name' => 'academy-level.create',
                'guard_name' => 'admin',
                'model' => 'academy-level',
                'required_permissions_slugs' => ['academy-level.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'academy levels',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'مستويات الأكاديمية',
                    ]
                ]
            ],
            [
                'name' => 'academy-level.edit',
                'guard_name' => 'admin',
                'model' => 'academy-level',
                'required_permissions_slugs' => ['academy-level.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'academy levels',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'مستويات الأكاديمية',
                    ]
                ]
            ],
            [
                'name' => 'academy-level.delete',
                'guard_name' => 'admin',
                'model' => 'academy-level',
                'required_permissions_slugs' => ['academy-level.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'academy levels',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'مستويات الأكاديمية',
                    ]
                ]
            ],

            // player-footnesse
            [
                'name' => 'player-footnesse.view',
                'guard_name' => 'admin',
                'model' => 'player-footnesse',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'player footnesses',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'الأقدام المفضلة',
                    ]
                ]
            ],
            [
                'name' => 'player-footnesse.create',
                'guard_name' => 'admin',
                'model' => 'player-footnesse',
                'required_permissions_slugs' => ['player-footnesse.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'player footnesses',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'الأقدام المفضلة',
                    ]
                ]
            ],
            [
                'name' => 'player-footnesse.edit',
                'guard_name' => 'admin',
                'model' => 'player-footnesse',
                'required_permissions_slugs' => ['player-footnesse.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'player footnesses',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'الأقدام المفضلة',
                    ]
                ]
            ],
            [
                'name' => 'player-footnesse.delete',
                'guard_name' => 'admin',
                'model' => 'player-footnesse',
                'required_permissions_slugs' => ['player-footnesse.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'player footnesses',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'الأقدام المفضلة',
                    ]
                ]
            ],

            // player-position
            [
                'name' => 'player-position.view',
                'guard_name' => 'admin',
                'model' => 'player-position',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'player position',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'أماكن الملعب',
                    ]
                ]
            ],
            [
                'name' => 'player-position.create',
                'guard_name' => 'admin',
                'model' => 'player-position',
                'required_permissions_slugs' => ['player-position.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'player position',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'أماكن الملعب',
                    ]
                ]
            ],
            [
                'name' => 'player-position.edit',
                'guard_name' => 'admin',
                'model' => 'player-position',
                'required_permissions_slugs' => ['player-position.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'player position',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'أماكن الملعب',
                    ]
                ]
            ],
            [
                'name' => 'player-position.delete',
                'guard_name' => 'admin',
                'model' => 'player-position',
                'required_permissions_slugs' => ['player-position.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'player position',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'أماكن الملعب',
                    ]
                ]
            ],

            // trainer-experience-level
            [
                'name' => 'trainer-experience-level.view',
                'guard_name' => 'admin',
                'model' => 'trainer-experience-level',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'trainer experience level',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'مستويات خبرة المدرب',
                    ]
                ]
            ],
            [
                'name' => 'trainer-experience-level.create',
                'guard_name' => 'admin',
                'model' => 'trainer-experience-level',
                'required_permissions_slugs' => ['trainer-experience-level.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'trainer experience level',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'مستويات خبرة المدرب',
                    ]
                ]
            ],
            [
                'name' => 'trainer-experience-level.edit',
                'guard_name' => 'admin',
                'model' => 'trainer-experience-level',
                'required_permissions_slugs' => ['trainer-experience-level.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'trainer experience level',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'مستويات خبرة المدرب',
                    ]
                ]
            ],
            [
                'name' => 'trainer-experience-level.delete',
                'guard_name' => 'admin',
                'model' => 'trainer-experience-level',
                'required_permissions_slugs' => ['trainer-experience-level.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'trainer experience level',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'مستويات خبرة المدرب',
                    ]
                ]
            ],

            // blog
            [
                'name' => 'blog.view',
                'guard_name' => 'admin',
                'model' => 'blog',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'blog',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'المقالات',
                    ]
                ]
            ],
            [
                'name' => 'blog.create',
                'guard_name' => 'admin',
                'model' => 'blog',
                'required_permissions_slugs' => ['blog.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'blog',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'المقالات',
                    ]
                ]
            ],
            [
                'name' => 'blog.edit',
                'guard_name' => 'admin',
                'model' => 'blog',
                'required_permissions_slugs' => ['blog.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'blog',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'المقالات',
                    ]
                ]
            ],
            [
                'name' => 'blog.delete',
                'guard_name' => 'admin',
                'model' => 'blog',
                'required_permissions_slugs' => ['blog.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'blog',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'المقالات',
                    ]
                ]
            ],

            // tag
            [
                'name' => 'tag.view',
                'guard_name' => 'admin',
                'model' => 'tag',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'tag',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'هاشتاجات',
                    ]
                ]
            ],
            [
                'name' => 'tag.create',
                'guard_name' => 'admin',
                'model' => 'tag',
                'required_permissions_slugs' => ['tag.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'tag',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'هاشتاجات',
                    ]
                ]
            ],
            [
                'name' => 'tag.edit',
                'guard_name' => 'admin',
                'model' => 'tag',
                'required_permissions_slugs' => ['tag.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'tag',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'هاشتاجات',
                    ]
                ]
            ],
            [
                'name' => 'tag.delete',
                'guard_name' => 'admin',
                'model' => 'tag',
                'required_permissions_slugs' => ['tag.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'tag',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'هاشتاجات',
                    ]
                ]
            ],

            // user
            [
                'name' => 'user.view',
                'guard_name' => 'admin',
                'model' => 'user',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'user',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'مستخدمين',
                    ]
                ]
            ],
            [
                'name' => 'user.accept-reject',
                'guard_name' => 'admin',
                'model' => 'user',
                'required_permissions_slugs' => ['user.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'accept / reject',
                        'group' => 'user',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'قبول / رفض',
                        'group' => 'مستخدمين',
                    ]
                ]
            ],
            [
                'name' => 'user.block-unblock',
                'guard_name' => 'admin',
                'model' => 'user',
                'required_permissions_slugs' => ['user.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'block / unblock',
                        'group' => 'user',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حظر / إلغاء حظر',
                        'group' => 'مستخدمين',
                    ]
                ]
            ],

            // academy
            [
                'name' => 'academy.view',
                'guard_name' => 'admin',
                'model' => 'academy',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'academies',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'حسابات الأكاديميات',
                    ]
                ]
            ],

            // player
            [
                'name' => 'player.view',
                'guard_name' => 'admin',
                'model' => 'player',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'players',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'حسابات اللاعبين الناشئين',
                    ]
                ]
            ],

            // trainer
            [
                'name' => 'trainer.view',
                'guard_name' => 'admin',
                'model' => 'trainers',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'trainer',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'مدربين',
                    ]
                ]
            ],

            // journalist
            [
                'name' => 'journalist.view',
                'guard_name' => 'admin',
                'model' => 'journalist',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'journalists',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'حسابات الإعلاميين',
                    ]
                ]
            ],

            // business
            [
                'name' => 'business.view',
                'guard_name' => 'admin',
                'model' => 'business',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'businesses',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'حسابات تجارية',
                    ]
                ]
            ],

            // influencer
            [
                'name' => 'influencer.view',
                'guard_name' => 'admin',
                'model' => 'influencer',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'influencers',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'حسابات المؤثرين',
                    ]
                ]
            ],

            // fan
            [
                'name' => 'fan.view',
                'guard_name' => 'admin',
                'model' => 'fan',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'fans',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'حسابات المشجعين',
                    ]
                ]
            ],

            // club
            [
                'name' => 'club.view',
                'guard_name' => 'admin',
                'model' => 'club',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'clubs',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'حسابات الأندية',
                    ]
                ]
            ],
            [
                'name' => 'club.create',
                'guard_name' => 'admin',
                'model' => 'club',
                'required_permissions_slugs' => ['club.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'clubs',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'حسابات الأندية',
                    ]
                ]
            ],
            [
                'name' => 'club.edit',
                'guard_name' => 'admin',
                'model' => 'club',
                'required_permissions_slugs' => ['club.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'clubs',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'حسابات الأندية',
                    ]
                ]
            ],

            // federation
            [
                'name' => 'federation.view',
                'guard_name' => 'admin',
                'model' => 'federation',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'federations',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'حسابات الإتحادات',
                    ]
                ]
            ],
            [
                'name' => 'federation.create',
                'guard_name' => 'admin',
                'model' => 'federation',
                'required_permissions_slugs' => ['federation.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'federations',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'حسابات الإتحادات',
                    ]
                ]
            ],
            [
                'name' => 'federation.edit',
                'guard_name' => 'admin',
                'model' => 'federation',
                'required_permissions_slugs' => ['federation.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'federations',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'حسابات الإتحادات',
                    ]
                ]
            ],
            [
                'name' => 'federation.delete',
                'guard_name' => 'admin',
                'model' => 'federation',
                'required_permissions_slugs' => ['federation.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'federations',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'حسابات الإتحادات',
                    ]
                ]
            ],

            // report
            [
                'name' => 'report.view',
                'guard_name' => 'admin',
                'model' => 'report',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'reports',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'تبليغات',
                    ]
                ]
            ],
            [
                'name' => 'report.delete',
                'guard_name' => 'admin',
                'model' => 'report',
                'required_permissions_slugs' => ['report.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'reports',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'تبليغات',
                    ]
                ]
            ],
            [
                'name' => 'report.action',
                'guard_name' => 'admin',
                'model' => 'report',
                'required_permissions_slugs' => ['report.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'process',
                        'group' => 'reports',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'معاملة',
                        'group' => 'تبليغات',
                    ]
                ]
            ],

            // sound
            [
                'name' => 'sound.view',
                'guard_name' => 'admin',
                'model' => 'sound',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'sounds',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'مقاطع صوتية',
                    ]
                ]
            ],
            [
                'name' => 'sound.create',
                'guard_name' => 'admin',
                'model' => 'sound',
                'required_permissions_slugs' => ['sound.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'sounds',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'مقاطع صوتية',
                    ]
                ]
            ],
            [
                'name' => 'sound.edit',
                'guard_name' => 'admin',
                'model' => 'sound',
                'required_permissions_slugs' => ['sound.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'sounds',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'مقاطع صوتية',
                    ]
                ]
            ],
            [
                'name' => 'sound.delete',
                'guard_name' => 'admin',
                'model' => 'sound',
                'required_permissions_slugs' => ['sound.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'sounds',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'مقاطع صوتية',
                    ]
                ]
            ],

            // contact
            [
                'name' => 'contact.view',
                'guard_name' => 'admin',
                'model' => 'contact',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'contacts',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'رسائل',
                    ]
                ]
            ],
            [
                'name' => 'contact.delete',
                'guard_name' => 'admin',
                'model' => 'contact',
                'required_permissions_slugs' => ['contact.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'contacts',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'رسائل',
                    ]
                ]
            ],

            // sticker
            [
                'name' => 'sticker.view',
                'guard_name' => 'admin',
                'model' => 'sticker',
                'required_permissions_slugs' => [],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'stickers',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'ملصقات',
                    ]
                ]
            ],
            [
                'name' => 'sticker.create',
                'guard_name' => 'admin',
                'model' => 'sticker',
                'required_permissions_slugs' => ['sticker.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'create',
                        'group' => 'stickers',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'اضافة',
                        'group' => 'ملصقات',
                    ]
                ]
            ],
            [
                'name' => 'sticker.edit',
                'guard_name' => 'admin',
                'model' => 'sticker',
                'required_permissions_slugs' => ['sticker.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'edit',
                        'group' => 'stickers',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'تعديل',
                        'group' => 'ملصقات',
                    ]
                ]
            ],
            [
                'name' => 'sticker.delete',
                'guard_name' => 'admin',
                'model' => 'sticker',
                'required_permissions_slugs' => ['sticker.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'delete',
                        'group' => 'stickers',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'حذف',
                        'group' => 'ملصقات',
                    ]
                ]
            ],

            // promote
            [
                'name' => 'promote.view',
                'guard_name' => 'admin',
                'model' => 'promote',
                'required_permissions_slugs' => ['promote.view'],
                'locales' => [
                    [
                        'locale' => 'en',
                        'name' => 'view',
                        'group' => 'promotes',
                    ],
                    [
                        'locale' => 'ar',
                        'name' => 'عرض',
                        'group' => 'إعلانات',
                    ]
                ]
            ],
        ];

        foreach ($data as $item) {
            $permission = Permission::create([
                'name' => $item['name'],
                'guard_name' => $item['guard_name'],
                'model' => $item['model'],
                'required_permissions_slugs' => $item['required_permissions_slugs'],
            ]);

            $routes = RoutePermission::select('route_slug')->where('permission_slug', $item['name']);

            foreach ($item['locales'] as $locale) {
                $permission->locales()->updateOrCreate(['locale' => $locale['locale']], $locale);
            }
        }

        Role::find(1)->givePermissionTo(Permission::where('guard_name', 'admin')->get());
    }
}
