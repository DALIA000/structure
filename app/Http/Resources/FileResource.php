<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    public function toArray($request)
    {
            return [
                'id' => $this->id,
                'url' => $this->original_url,
                'webp' => $this->getUrl('media'),
                'cover' => $this->getUrl('cover'),
                "name" => $this->name,
                'file_mimetype' => $this->mime_type,
                'size' => $this->size,
            ];
    }
}
