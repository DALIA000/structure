<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait Imagable
{
  public function images()
  {
    return $this->morphMany(Image::class, 'imagable');
  }

  public function image()
  {
    return $this->morphOne(Image::class, 'imagable');
  }
  
  public function imageUrl(): Attribute
  {
    $image = $this->image;
      return Attribute::make(
        get: fn() => $image ? $image->image_url : null,
      );
    }
}