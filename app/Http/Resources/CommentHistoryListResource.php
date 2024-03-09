<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{
    Video,
    Blog,
    Comment
};

class CommentHistoryListResource extends JsonResource
{
    public function toArray($request)
    {
        switch ($this->commentable) {
            case $this->commentable instanceof Video:
                $type = 'video';
                break;

            case $this->commentable instanceof Blog:
                $type = 'blog';
                break;

            case $this->commentable instanceof Comment:
                $type = 'comment';
                break;
            
            default:
                $type = null;
                break;
        }
        return [
            'type' => $type,
            'model' => [
                'id' => $this->commentable?->id,
                'user' => new UsersShortListResource($this->commentable?->user),
                'title' => $this->commentable?->locale?->name,
            ]
        ];
    }
}
