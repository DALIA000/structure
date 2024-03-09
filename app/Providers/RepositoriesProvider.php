<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoriesProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Http\Repositories\Auth\AuthInterface::class, \App\Http\Repositories\Auth\AuthRepository::class);
        $this->app->bind(\App\Http\Repositories\Country\CountryInterface::class, \App\Http\Repositories\Country\CountryRepository::class);
        $this->app->bind(\App\Http\Repositories\City\CityInterface::class, \App\Http\Repositories\City\CityRepository::class);
        $this->app->bind(\App\Http\Repositories\AcademyLevel\AcademyLevelInterface::class, \App\Http\Repositories\AcademyLevel\AcademyLevelRepository::class);
        $this->app->bind(\App\Http\Repositories\PlayerFootness\PlayerFootnessInterface::class, \App\Http\Repositories\PlayerFootness\PlayerFootnessRepository::class);
        $this->app->bind(\App\Http\Repositories\PlayerPosition\PlayerPositionInterface::class, \App\Http\Repositories\PlayerPosition\PlayerPositionRepository::class);
        $this->app->bind(\App\Http\Repositories\TrainerExperienceLevel\TrainerExperienceLevelInterface::class, \App\Http\Repositories\TrainerExperienceLevel\TrainerExperienceLevelRepository::class);
        $this->app->bind(\App\Http\Repositories\Setting\SettingInterface::class, \App\Http\Repositories\Setting\SettingRepository::class);
        $this->app->bind(\App\Http\Repositories\Blog\BlogInterface::class, \App\Http\Repositories\Blog\BlogRepository::class);
        $this->app->bind(\App\Http\Repositories\Tag\TagInterface::class, \App\Http\Repositories\Tag\TagRepository::class);
        $this->app->bind(\App\Http\Repositories\ClubPlan\ClubPlanInterface::class, \App\Http\Repositories\ClubPlan\ClubPlanRepository::class);
        $this->app->bind(\App\Http\Repositories\Sound\SoundInterface::class, \App\Http\Repositories\Sound\SoundRepository::class);
    }
}
