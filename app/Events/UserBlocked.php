<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\{
    Block
};

class UserBlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user; // user who did the action
    public $participants; // blockables
    public $action;
    public $video;
    public $model;

    public $data;

    public function __construct(public Block $block, $note = null)
    {
        $this->action = $action = 'block';

        $this->user = $block->user;
        $this->participants = [$block->blockable];

        $model = [
            'id' => $block->id,
            'type' => 'block'
        ];

        $this->model = $model;

        $this->data = [
            'full_name' => $this->user?->user?->business_name ?: "{$this->user?->user?->first_name} {$this->user?->user?->last_name}",
            'username' => $this->user?->username,
            'action' => $action,
            'model' => $model,
            'note' => $note,
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications');
    }
}
