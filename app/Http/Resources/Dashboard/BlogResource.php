<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => new StatusListResource($this->status),
            'locales' => LocaleResource::collection($this->locales),
            'media' => $this->files?->count() ? new FileResource($this->files[0]) : null,
            'tags' => $this->tags->pluck('id'),
            'trashed' => (Boolean) $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
