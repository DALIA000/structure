<?php

namespace App\Http\Resources\Dashboard;

use App\Models\File;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademyPresidentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
   {
        $file = $this->files && $this->files[0] ? $this->files[0] : null;
        return [
            'full_name' => $this->full_name,
            'media' => $file ? new FileResource($file) : new UserFileResource(File::find(1)),
        ];
  }
}
