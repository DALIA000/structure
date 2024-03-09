<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscribtionsListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'plan' => new ClubPlansListResource($this->plan),
            'status' => $this->status,
        ];
    }
}
