<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'user' => new UsersShortListResource($this->user),
            'comments_count' => $this->comments_count,
            'likes_count' => $this->likes_count,
            'has_liked' => (boolean) $this->has_liked->count(),
            'created_at' => $this->created_at,
            'since' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null,
        ];
    }
}
