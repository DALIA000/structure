<?php

namespace App\Events;

use App\Http\Resources\Dashboard\FileResource;
use App\Http\Resources\Dashboard\UserFileResource;
use App\Models\File;
use App\Models\Follow;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserFollowed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $participants;
    public $action;
    public $followable;
    public $model;

    public $data;

    public function __construct(public Follow $follow, $note = null)
    {
        $followable = $follow->followable;
        $this->action = $action = $follow->is_pending ? 'follow_request' : 'follow';

        $this->user = $follow->user;
        $this->participants = [$followable];

        $model = [
            'id' => null,
            'type' => $follow->is_pending ? 'follow_request' : 'follow'
        ];

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
