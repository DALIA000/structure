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
    File,
    User
};

class UserRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user; // user who did the action
    public $participants;
    public $action;
    public $video;
    public $model;

    public $data;

    public function __construct(public User $account, $note = null)
    {
        $this->action = $action = 'user_rejected';

        $this->user = null;
        $this->participants = [$account];

        $model = [
            'id' => $account->id,
            'type' => 'user'
        ];

        $this->model = $model;
        $file = $account->getFirstMedia() ? new FileResource($account?->getFirstMedia()) : new UserFileResource(File::find(1));

        $name = "{$this->user?->user?->first_name} {$this->user?->user?->last_name}";
        $name = !($name) || $name == " " ? null: $name;
        $this->data = [
            'full_name' => $this->user?->user?->business_name ?: $name ?: null,
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
