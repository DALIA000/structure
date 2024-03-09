<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = [
            [
                'name' => 'info',
                'email' => 'info@remontadafc.com',
                'username' => 'info',
                'password' => \Hash::make('Remontada2023#'),
            ],
            [
                'name' => 'developer',
                'email' => 'developer@developer.com',
                'username' => 'developer',
                'password' => \Hash::make('password!2P'),
            ]
        ];
        foreach ($admins as $admin) {
            $data = \App\Models\Admin::create($admin);
            $data->assignRole('manager');
        }
    }
}
