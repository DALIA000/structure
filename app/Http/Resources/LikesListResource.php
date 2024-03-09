<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{
    Model,
    Video,
    Blog,
    Comment
};

class LikesListResource extends JsonResource
{
    public function toArray($request)
    {
        switch ($this->likable_type) {
            case Video::class:
                return new VideosListResource($this->likable);
            
            case Blog::class:
                return new BlogsListResource($this->likable);

            case Comment::class:
                return new CommentsWithModelListResource($this->likable);
            
            default:
                return [];
        };
    }
}
