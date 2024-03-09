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
    VideoTag
};

class UserTaged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $participants;
    public $action;
    public $video;
    public $model;

    public $data;

    public function __construct(public VideoTag $video_tag, $note = null)
    {
        $video = $video_tag->video;
        $this->action = $action = 'video_tag';

        $this->user = $video_tag->user;
        $this->participants = [$video->user];

        $model = [
            'id' => $video->id,
            'type' => 'video'
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
