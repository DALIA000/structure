<?php

namespace App\Http\Resources\Dashboard;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubResource extends JsonResource
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
            'phone' => $this->account?->phone,
            'country' => $this->account?->city?->country?->slug,
            'city' => $this->account?->city?->slug,
            'status' => $this->status?->slug,
            'birthday' => $this->account?->birthday,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'description' => $this->description,
            'club_players_count' => $this->club_players_count,
            'club_president' => new ClubPresidentResource($this->club_president),
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'subscribers_count' => $this->subscribers_count,
            'subscribtions_count' => $this->subscribtions_count,
            'created_at' => $this->account?->created_at,
        ];
    }
}
