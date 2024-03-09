<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class ClubPlayerResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'club' => $this->club_id,
            'player_position' => $this->player_position_id,
            'media' => $file ? new FileResource($file) : null,
        ];
    }
}
