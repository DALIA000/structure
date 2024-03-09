<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class ClubPlansListResource extends JsonResource
{
  public function toArray($request)
  {
    return [
        'id' => $this->id,
        'type' => new ClubPlanTypesListResource($this->club_plan_type),
        'price' => $this->price,
        'description' => $this->locale?->description,
        'locales' => $this->localize($this->locales),
        'subscribers_count' => $this->subscribers_count,
        'subscribtions_count' => $this->subscribtions_count,
    ];
  }
}
