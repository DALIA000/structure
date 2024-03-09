<?php

namespace App\Http\Resources;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubsListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->files && $this->files[0] ? $this->files[0] : null;

        return [
            'username' => $this->account?->username,
            'business_name' => $this->business_name,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
        ];
    }
}
