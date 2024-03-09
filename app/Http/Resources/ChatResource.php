<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'read_at' => $this->read_at,
            'since' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans(null, true, true) : null,
            'is_seen' => $this->is_seen,
            'is_sender' => $this->is_sender,
            'type' => $this->type,
            'sender' => new UserChatListResource($this->sender),
            'created_at' => $this->created_at,
        ];
    }
}
