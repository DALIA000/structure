<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class JournalistsListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->account->files && $this->account->files[0] ? $this->account->files[0] : null;

        return [
            'user_id' => $this->user_id,
            'id' => $this->id,
            'username' => $this->account?->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->account?->email,
            'city' => $this->account?->city?->locale?->name,
            'status' => $this->status?->slug,
            'media' => $file ? new FileResource($file) : null,
            'created_at' => $this->account?->created_at,
        ];
    }
}
