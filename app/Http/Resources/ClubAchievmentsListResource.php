<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClubAchievmentsListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'year' => $this->year,
            'media' => $file ? new FileResource($file) : null,
        ];
    }
}
