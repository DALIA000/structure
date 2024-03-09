<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClubPlanResource extends JsonResource
{
  public function toArray($request)
  {
    return [
        'id' => $this->id,
        'club' => new ClubsListResource($this->club),
        'type' => new ClubPlanTypesListResource($this->club_plan_type),
        'price' => $this->price,
        'description' => $this->locale?->description,
    ];
  }
}
