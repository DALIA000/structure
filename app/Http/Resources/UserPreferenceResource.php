<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'preference' => $this->preference?->slug,
      'slug' => $this->preference?->slug,
      'name' => $this->preference?->locale?->name,
      'value' => $this->value,
    ];
  }
}
