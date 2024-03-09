<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'document' => url($this->document),
            'document_name' => $this->document_name,
            'document_mimetype' => $this->document_mimetype,
            'size' => $this->size,
        ];
    }
}
