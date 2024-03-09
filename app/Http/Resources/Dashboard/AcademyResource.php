<?php

namespace App\Http\Resources\Dashboard;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademyResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->account->files && $this->account->files[0] ? $this->account->files[0] : null;

        return [
            'user_id' => $this->user_id,
            'id' => $this->id,
            'username' => $this->account?->username,
            'birthday' => $this->account?->birthday,
            'business_name' => $this->business_name,
            'email' => $this->account?->email,
            'phone' => $this->account?->phone,
            'city' => $this->account?->city?->locale?->name,
            'country' => $this->account?->city?->country->locale?->name,
            'latitude' =>  $this->latitude,
            'longitude' =>  $this->longitude,
            'description' =>  $this->description,
            'academy_level' => new AcademyLevelsListResource($this->academy_level),
            'status' => $this->status?->slug,
            'players_count' => $this->players_count,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'certificates' => [
                'commercial_certification' => new CertificationResource($this->account?->certification?->commercial_certification),
            ],
            'created_at' => $this->account?->created_at,
        ];
    }
}
