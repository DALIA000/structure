<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionsListResource extends JsonResource
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
            'slug' => $this->name,
            'name' => $this->locale?->name,
            'model' => $this->model,
            'group' => $this->locale?->group,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
