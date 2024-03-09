<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country = \App\Models\Country::create([
            "slug" => "sa",
            "timezone" => "Asia/Riyadh",
        ]);
        $country->locales()->create([
            "locale" => "en",
            "name" => "Saudi Arabia",
        ]);
        $country->locales()->create([
            "locale" => "ar",
            "name" => "المملكة العربية السعودية",
        ]);

        $country = \App\Models\Country::create([
            "slug" => "egypt",
            "timezone" => "Asia/Riyadh",
        ]);
        $country->locales()->create([
            "locale" => "en",
            "name" => "Egypt",
        ]);
        $country->locales()->create([
            "locale" => "ar",
            "name" => "مصر",
        ]);
    }
}
