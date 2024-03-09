<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'file' => $this->getFullUrl(),
            'cover' => $this->getFullUrl('cover'),
            'file_name' => $this->file_name,
            'file_mimetype' => $this->mime_type,
            'size' => $this->size,
        ];
    }
}
