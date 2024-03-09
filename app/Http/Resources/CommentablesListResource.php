<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\{
    Video,
    Blog
};

class CommentablesListResource extends JsonResource
{
    public function toArray($request)
    {
        switch ($this->resource) {
            case $this->resource instanceof Video:
                return [
                    'id' => $this->id,
                    'user' => [
                        'username' => $this->user?->username,
                        'has_followed' => (boolean)$this->user?->has_followed->count(),
                    ],
                    'type' => 1, // video
                ];
            
            case $this->resource instanceof Blog:
                return [
                    'id' => $this->id,
                    'name' => $this->locale->name,
                    'type' => 2, // blog
                ];

            default:
                return [];
        };
    }
}
