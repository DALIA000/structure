<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'locales' => $this->localize($this->locales),
            "country_id" => $this->country_id,
        ];
    }
}
