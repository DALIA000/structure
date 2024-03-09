<?php

namespace Database\Seeders;

use App\Models\File;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, 10) as $index) {
            $file = File::create();
            $file->addMedia(public_path('images/user-default.png'))->toMediaCollection('media');
            \Illuminate\Support\Facades\File::copy(public_path('images/user-default2.png'), public_path('images/user-default.png'));
        }
    }
}
