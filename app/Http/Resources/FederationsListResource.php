<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FederationsListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'username' => $this->account?->username,
            'business_name' => $this->business_name,
        ];
    }
}
