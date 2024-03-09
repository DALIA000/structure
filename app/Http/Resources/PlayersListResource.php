<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\File;

class PlayersListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->account->files && $this->account->files[0] ? $this->account->files[0] : null;
        return [
            'username' => $this->account?->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'player_position' => $this->player_position?->locale?->name,
            'strength_points' => $this->academy_player?->strength_points
        ];
    }
}
