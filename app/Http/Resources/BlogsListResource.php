<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogsListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->locale?->name,
            'media' => $this->files?->count() ? new FileResource($this->files[0]) : null,
            'tags' => $this->tags->pluck('name'),
            'likes_count' => $this->likes_count,
            'has_liked' => (boolean) $this->has_liked->count(),
            'has_saved' => (boolean) $this->has_saved->count(),
            'comments_count' => $this->comments_count,
            'views_count' => $this->views_count,
            'status' => new StatusListResource($this->status),
            'created_at' => $this->created_at,
        ];
    }
}
