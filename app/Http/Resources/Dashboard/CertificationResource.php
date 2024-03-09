<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class CertificationResource extends JsonResource
{
    public function toArray($request)
    {
        $type = substr($this->mime_type, 0, strpos($this->mime_type, '/'));
        return [
            'id' => $this->id,
            'url' => url($this->document),
            "name" => $this->document_name,
            "mimetype" => $this->document_mimetype,
            "size" => $this->size,
        ];
    }
}
