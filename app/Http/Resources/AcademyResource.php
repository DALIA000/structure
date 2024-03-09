<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AcademyResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        return [
            'username' => $this->username,
            'business_name' => $user?->business_name,
            'latitude' => $user?->latitude,
            'longitude' => $user?->longitude,
            'birthday' => $this->birthday,
            'city' => $this->city?->locale?->name,
            'description' => $user?->description,
            'academy_level' => new AcademyLevelResource($user?->academy_level),
            'academy_president' => new AcademyPresidentResource($user?->academy_president)
        ];
    }
}
