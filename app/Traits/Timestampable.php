<?php

namespace App\Traits;

use Carbon\Carbon;
use DateTime;

trait Timestampable
{
  public function getCreatedAtAttribute($value)
  {
    return $value ? Carbon::parse($value)->timezone(config('APP_TIMEZONE'))->format(DateTime::ISO8601) : null;
  }

  public function getUpdatedAtAttribute($value)
  {
    return $value ? Carbon::parse($value)->timezone(config('APP_TIMEZONE'))->format(DateTime::ISO8601) : null;
  }
}
