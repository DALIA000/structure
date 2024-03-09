<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerOvervievResource extends JsonResource
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
            'videos_count' => $this->account?->videos_count,
            'tagged_videos_count' => $this->account?->tagged_videos_count,
            'followings_count' => $this->account?->followings_count,
            'followers_count' => $this->account?->followers_count,
        ];
    }
}
