<?php

namespace App\Http\Resources;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademyPlayerResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->player?->account->files && $this->player?->account->files[0] ? $this->account->files[0] : null;

        return [
            'username' => $this->player?->account?->username,
            'first_name' => $this->player?->first_name,
            'last_name' => $this->player?->last_name,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'strength_points' => $this->strength_points,
            'status' => $this->status?->slug,
            'created_at' => $this->account?->created_at,
        ];
    }
}
