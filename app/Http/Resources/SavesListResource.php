<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{
    Model,
    Video,
    Blog,
    User
};

class SavesListResource extends JsonResource
{
    public function toArray($request)
    {
        switch ($this->savable_type) {
            case Video::class:
                return new VideosListResource($this->savable);
            
            case Blog::class:
                return new BlogsListResource($this->savable);

            case User::class:
                return new UserMinifiedResource($this->savable);
            
            default:
                return [];
        };
    }
}
