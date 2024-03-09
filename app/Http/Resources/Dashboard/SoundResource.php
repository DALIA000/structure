<?php

namespace App\Http\Resources\Dashboard;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SoundResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'sound' => url($this->sound),
            'image' => url($this->image),
            'singer' => $this->singer,
            'length' => $this->length,
            'size' => $this->size,
            'created_at' => $this->created_at->format('y/w/d'),
            'since' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null,
        ];
    }
}
