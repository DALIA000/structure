<?php

namespace App\Http\Resources;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubPresidentResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        return [
            'full_name' => $this->full_name,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
        ];
    }
}
