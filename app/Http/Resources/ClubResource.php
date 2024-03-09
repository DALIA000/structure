<?php

namespace App\Http\Resources;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        return [
            'username' => $this->account?->username,
            'business_name' => $this->business_name,
            'email' => $this->account?->email,
            'phone' => $this->account?->phone,
            'city' => $this->account?->city?->locale?->name,
            'birthday' => $this->account?->birthday,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'description' => $this->description,
            'club_players_count' => $this->club_players()->count(),
            'club_president' => new ClubPresidentResource($this->club_president),
            'competitions_count' => $this->competitions_count,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
        ];
    }
}
