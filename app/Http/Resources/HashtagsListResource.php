<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HashtagsListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'hashtag' => $this->hashtag,
            'count' => $this->count,
        ];
    }
}
