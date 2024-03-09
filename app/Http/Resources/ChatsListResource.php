<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatsListResource extends JsonResource
{
    public function toArray($request)
    {
        $loggedinUser = app('loggedinUser');
        return [
            'conversation_id' => $this->conversation_id,
            'unread_messages_count' => $this->conversation->unReadNotifications(app('loggedinUser'))->count(),
            'participant' => new UserChatListResource($this->conversation?->participants?->where('messageable.id', '!=', $loggedinUser?->id)?->first()?->messageable),
            'conversation' => [
                'last_message' => [
                    'message_id' => $this->conversation?->last_message?->message_id,
                    'is_seen' => $this->conversation?->last_message?->is_seen,
                    'is_sender' => $this->conversation?->last_message?->is_sender,
                    'created_at' => $this->conversation?->last_message?->created_at,
                    'body' => $this->conversation?->last_message?->body,
                    'sender' => new UserChatListResource($this->conversation?->last_message?->sender),
                ],
            ]
        ];
    }
}
