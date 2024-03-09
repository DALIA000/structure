<?php

namespace App\Events;

use App\Http\Resources\Dashboard\FileResource;
use App\Http\Resources\Dashboard\UserFileResource;
use App\Http\Resources\LikesListResource;
use App\Models\Like;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\{
    File,
    Video,
    Comment,
    Blog
};

class UserLikedVideo
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $participants;
    public $action;
    public $likable;
    public $model;

    public $data;

    public function __construct(public Like $like, $note = null)
    {
        $likable = $like->likable;
        $this->action = $action = 'like';

        $this->user = $like->user;
        $this->participants = [$likable?->user];

        switch (get_class($likable)) {
            case Video::class:
                $model = [
                    'id' => $likable?->id,
                    'type' => 'video'
                ];
                break;

            case Comment::class:
                $model = [
                    'id' => $likable?->id,
                    'type' => 'comment'
                ];
                break;

            case Blog::class:
                $model = [
                    'id' => $likable?->id,
                    'type' => 'blog'
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
