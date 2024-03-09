<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $type = substr($this->mime_type, 0, strpos($this->mime_type, '/'));
        if ($type == "image") {
            return [
                'id' => $this->id,
                'url' => $this->original_url,
                'webp' => $this->getUrl('media'),
                "name" => $this->name,
            ];
        } else {
            return [
                'id' => $this->id,
                'url' => $this->original_url,
                "name" => $this->name,
            ];
        }
    }
}
