<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountriesListResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'slug' => $this->slug,
      'name' => $this->locale?->name,
    ];
  }
}
