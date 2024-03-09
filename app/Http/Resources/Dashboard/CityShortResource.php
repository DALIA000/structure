<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class CityShortResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'slug' => $this->slug,
        ];
    }
}
