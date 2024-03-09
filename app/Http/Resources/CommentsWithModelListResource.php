<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentsWithModelListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'commentable' => new CommentablesListResource($this->commentable),
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null
        ];
    }
}
