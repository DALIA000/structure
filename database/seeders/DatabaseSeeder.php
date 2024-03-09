<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            LanguageSeeder::class,
            CurrencySeeder::class,
            StatusSeeder::class,
            FinancialSettingSeeder::class,
            SpamSectionSeeder::class,
            PreferenceTypeSeeder::class,
            PreferenceSeeder::class,
            ModelSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminSeeder::class,
            CountrySeeder::class,
            CitySeeder::class,
            SettingSeeder::class,
            UserTypeSeeder::class,
            AcademyLevelSeeder::class,
            PlayerFootnessSeeder::class,
            PlayerPositionSeeder::class,
            TrainerExperienceLevelSeeder::class,
            ClubPlanTypeSeeder::class,
            TagSeeder::class,
            MediaSeeder::class,
            SpamSeeder::class,
        ]);
    }
}
