<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'slug' => $this->slug,
      'name' => $this->locale?->name,
    ];
  }
}
