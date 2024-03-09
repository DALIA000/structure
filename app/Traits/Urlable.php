<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait Urlable
{
  public function imageUrl(): Attribute
  {
    return Attribute::make(
      get: fn () => $this->image ? url($this->image) : null,
    );
  }
}
