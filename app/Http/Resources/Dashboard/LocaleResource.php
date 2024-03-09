<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class LocaleResource extends JsonResource
{
    public $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $data = array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'short_description' => $this->short_description,
        ]);
        $data = [$this->locale => $data];
        return $this->merge($data);}
}
