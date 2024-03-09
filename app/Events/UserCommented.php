<?php

namespace App\Events;

use App\Http\Resources\Dashboard\FileResource;
use App\Http\Resources\Dashboard\UserFileResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\{
    Video,
    Comment,
    File,
};

class UserCommented
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user; // user who did the action
    public $participants;
    public $action;
    public $commentable;
    public $model;

    public $data;

    public function __construct(public Comment $comment, $note = null)
    {
        $commentable = $comment->commentable;
        $action = 'comment';

        $this->user = $comment->user;
        $this->participants = [$commentable->user];

        switch (get_class($commentable)) {
            case Video::class:
                $model = [
                    'id' => $comment->id,
                    'commentable_id' => $commentable->id,
                    'type' => 'video'
                ];
                break;

            case Comment::class:
                $model = [
                    'id' => $comment->id,
                    'commentable_id' => $commentable->id,
                    'type' => 'comment'
                ];
                break;

            default:
                $model = [];
                break;
        };

        $this->model = $model;
        $file = $this->user->getFirstMedia() ? new FileResource($this->user->getFirstMedia()) : new UserFileResource(File::find(1));

        $this->data = [
            'full_name' => $this->user?->user?->business_name ?: "{$this->user?->user?->first_name} {$this->user?->user?->last_name}",
            'username' => $this->user?->username,
            'action' => $action,
            'model' => $model,
            'note' => $note,
            'image' => $file,
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications');
    }
}
