<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{
    Video,
    Blog,
    Comment
};

class LikeHistoryListResource extends JsonResource
{
    public function toArray($request)
    {
        switch ($this->likable) {
            case $this->likable instanceof Video:
                $type = 'video';
                break;

            case $this->likable instanceof Blog:
                $type = 'blog';
                break;

            case $this->likable instanceof Comment:
                $type = 'comment';
                break;
            
            default:
                $type = null;
                break;
        }
        return [
            'type' => $type,
            'model' => [
                'id' => $this->likable?->id,
                'user' => new UsersShortListResource($this->likable?->user),
                'title' => $this->likable?->locale?->name,
            ]
        ];
    }
}
