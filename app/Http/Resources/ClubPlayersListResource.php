<?php

namespace App\Http\Resources;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubPlayersListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'player_position' => $this->player_position?->locale?->name,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
        ];
    }
}
