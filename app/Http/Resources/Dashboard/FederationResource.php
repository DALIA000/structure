<?php

namespace App\Http\Resources\Dashboard;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class FederationResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        return [
            'user_id' => $this->user_id,
            'id' => $this->id,
            'username' => $this->account?->username,
            'bio' => $this->account?->bio,
            'business_name' => $this->business_name,
            'email' => $this->account?->email,
            'city' => $this->account?->city?->locale?->name,
            'phone' => $this->account?->phone,
            'status' => $this->status?->slug,
            'birthday' => $this->account?->birthday,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'created_at' => $this->account?->created_at,
        ];
    }
}
