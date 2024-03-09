<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{
    User,
    Video,
    Blog,
    Comment
};

class SaveHistoryListResource extends JsonResource
{
    public function toArray($request)
    {
        switch ($this->savable) {
            case $this->savable instanceof User:
                $type = 'account';
                break;
            
            case $this->savable instanceof Video:
                $type = 'video';
                break;

            case $this->savable instanceof Blog:
                $type = 'blog';
                break;

            case $this->savable instanceof Comment:
                $type = 'comment';
                break;
            
            default:
                $type = null;
                break;
        }
        return [
            'type' => $type,
            'model' => [
                'id' => $this->savable?->id,
                'user' => $type == 'account' ? new UsersShortListResource($this->savable) : new UsersShortListResource($this->savable?->user),
                'title' => $this->savable?->locale?->name,
            ]
        ];
    }
}
