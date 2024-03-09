<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class SpamResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'locales' => $this->localize($this->locales),
      'spam_section' => $this->spam_section?->slug,
    ];
  }
}
