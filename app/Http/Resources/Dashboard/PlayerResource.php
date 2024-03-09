<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->account->files && $this->account->files[0] ? $this->account->files[0] : null;
        $academy = $this->academy;
        return [
            'user_id' => $this->user_id,
            'id' => $this->id,
            'username' => $this->account?->username,
            'bio' => $this->account?->bio,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->account?->email,
            'phone' => $this->account?->phone,
            'city' => $this->account?->city?->locale?->name,
            'academy' => $academy ? [
                'id' => $academy?->id,
                'username' => $academy?->account?->username,
                'business_name' => $academy?->business_name,
            ] : $this->other_academy,
            'player_position' => new PlayerPositionsListResource($this->player_position),
            'player_footness' => new PlayerFootnessesListResource($this->player_footness),
            'media' => $file ? new FileResource($file) : null,
            'status' => $this->status?->slug,
            'created_at' => $this->account?->created_at,
        ];
    }
}
