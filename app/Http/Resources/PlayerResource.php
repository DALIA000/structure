<?php

namespace App\Http\Resources;

use App\Http\Resources\Dashboard\AcademyResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray($request)
    {
        $user = $this->user;
        return [
            'username' => $this->username,
            'first_name' => $user?->first_name,
            'last_name' => $user?->last_name,
            'academy' => new AcademiesListResource($user?->academy_player?->academy) ?: $this->other_academy,
        ];
    }
}
