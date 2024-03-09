<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {


        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'vat' => $this->vat,
            'locales' => LocaleResource::collection($this->locales),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
