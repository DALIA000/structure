<?php

namespace App\Traits;

use App\Models\Locale;
use Carbon\Carbon;
use DateTime;

trait Localizable
{
    public function locales()
    {
        return $this->morphMany(Locale::class, 'localizable');
    }

    public function locale()
    {
        $localizables = isset(SELF::$localizables) 
                          ? ['id', 'localizable_id', 'localizable_type', 'locale', ...SELF::$localizables] 
                          : ['*'];

        return $this->morphOne(Locale::class, 'localizable')
                    ->select($localizables)
                    ->where('locale', app()->getLocale());
    }

    public function localize($locales)
    {
        return collect($locales->map(fn($locale) => $locale->only(SELF::$locales_columns)))->groupBy('locale')->map(fn($i) => $i ? collect($i[0])->except(['id', 'locale']) : null);;
    }

    public function getCreatedAtAttribute($value)
    {
        $loggedinUser = app('loggedinUser');
        $timezone = $loggedinUser?->city?->country?->timezone ?: 'Asia/Riyadh';
        return $value ? Carbon::parse($value)->timezone($timezone)->format(DateTime::ISO8601) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        $loggedinUser = app('loggedinUser');
        $timezone = $loggedinUser?->city?->country?->timezone ?: 'Asia/Riyadh';
        return $value ? Carbon::parse($value)->timezone($timezone)->format(DateTime::ISO8601) : null;
    }
}
