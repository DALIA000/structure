<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionsListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->video?->user?->files && $this->video?->user?->files[0] ? $this->video?->user?->files[0] : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'date' => $this->date,
            'time' => $this->time,
            'status' => $this->status,
        ];
    }
}
