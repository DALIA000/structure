<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class TrainerResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->account->files && $this->account->files[0] ? $this->account->files[0] : null;

        return [
            'user_id' => $this->user_id,
            'bio' => $this->account?->bio,
            'id' => $this->id,
            'username' => $this->account?->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'birthday' => $this->account?->birthday,
            'email' => $this->account?->email,
            'phone' => $this->account?->phone,
            'city' => $this->account?->city?->locale?->name,
            'country' => $this->account?->city?->country?->locale?->name,
            'status' => $this->status?->slug,
            'trainer_experience_level' => new TrainerExperienceLevelsListResource($this->trainer_experience_level),
            'achievements' => $this->achievements,
            'certificates' => [
                'training_certification' => new CertificationResource($this->account?->certification?->training_certification),
                'experience_certification' => new CertificationResource($this->account?->certification?->experience_certification),
            ],
            'media' => $file ? new FileResource($file) : null,
            'created_at' => $this->account?->created_at,
        ];
    }
}
