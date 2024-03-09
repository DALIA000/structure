<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserFileResource extends JsonResource
{
    public function toArray($request)
    {
        $media = $this->media->first();
        return [
            'id' => $this->id,
            'url' => $media?->original_url,
            'webp' => $media?->getUrl('media'),
            "name" => $media?->name,
        ];
    }
}
