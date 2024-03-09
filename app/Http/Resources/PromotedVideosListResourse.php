<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotedVideosListResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $daysBetween = Carbon::parse($this->ends_at)->diffInDays(now());

        return [
            'model' => new VideosListResource($this->promotable),
            'status' => (boolean) $this->status,
            'created_at' => $this->created_at->format('Y/m/d'),
            'remaining_days' => $daysBetween,
            'since' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null,
        ];
    }
}
