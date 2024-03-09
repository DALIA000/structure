<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Model;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoutePermission;

class TempSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // sticker 
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
