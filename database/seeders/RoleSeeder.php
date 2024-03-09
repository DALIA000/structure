<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'name' => 'manager',
                'guard_name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('locales')->insert([
            [
                'locale' => 'en',
                'localizable_type' => Role::class,
                'localizable_id' => 1,
                'name' => 'manager',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'locale' => 'ar',
                'localizable_type' => Role::class,
                'localizable_id' => 1,
                'name' => 'مدير عام',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
