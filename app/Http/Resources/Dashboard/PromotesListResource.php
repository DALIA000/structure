<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class PromotesListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'model' => new VideosListResource($this->promotable),
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'target' => $this->target,
            'cities' => CitiesListResource::collection($this->cities),
            'user_types' => UserTypeResource::collection($this->user_types),
        ];
    }
}
