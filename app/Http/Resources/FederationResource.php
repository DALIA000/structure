<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FederationResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        return [
            'username' => $this->username,
            'business_name' => $user?->business_name,
            'latitude' => $user?->latitude,
            'longitude' => $user?->longitude,
            'birthday' => $this->account?->birthday,
            'city' => $this->city?->locale?->name,
            'description' => $user?->description,
            'academy_president' => new FederationPresidentResource($user?->federation_president)
        ];
    }
}
