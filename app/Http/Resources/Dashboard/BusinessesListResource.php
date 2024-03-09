<?php

namespace App\Http\Resources\Dashboard;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessesListResource extends JsonResource
{
    public function toArray($request)
    {
        $file = $this->account->files && $this->account->files[0] ? $this->account->files[0] : null;

        return [
            'user_id' => $this->user_id,
            'id' => $this->id,
            'username' => $this->account?->username,
            'business_name' => $this->business_name,
            'email' => $this->account?->email,
            'city' => $this->account?->city?->locale?->name,
            'status' => $this->status?->slug,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
            'created_at' => $this->account?->created_at,
        ];
    }
}
