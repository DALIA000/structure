<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $city = City::create([
            "country_id" => 1,
            "slug" => "riyadh",
        ]);
        $city->locales()->create([
            "locale" => "en",
            "name" => "Riyadh",
        ]);
        $city->locales()->create([
            "locale" => "ar",
            "name" => "الرياض",
        ]);

        $city = City::create([
            "country_id" => 1,
            "slug" => "jeddah",
        ]);
        $city->locales()->create([
            "locale" => "en",
            "name" => "Jeddah",
        ]);
        $city->locales()->create([
            "locale" => "ar",
            "name" => "جدة",
        ]);


        $city = City::create([
            "country_id" => 2,
            "slug" => "cairo",
        ]);
        $city->locales()->create([
            "locale" => "en",
            "name" => "Cairo",
        ]);
        $city->locales()->create([
            "locale" => "ar",
            "name" => "القاهرة",
        ]);

        $city = City::create([
            "country_id" => 2,
            "slug" => "alexandria",
        ]);
        $city->locales()->create([
            "locale" => "en",
            "name" => "Alexandria",
        ]);
        $city->locales()->create([
            "locale" => "ar",
            "name" => "اسكندرية",
        ]);
    }
}
