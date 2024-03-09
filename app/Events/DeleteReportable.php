<?php

namespace App\Events;

use App\Http\Resources\Dashboard\UserFileResource;
use App\Models\Report;
use App\Models\Video;
use App\Models\Comment;
use App\Models\File;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeleteReportable
{
   use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $participants;
    public $action;
    public $reportable;
    public $model;
    public $data;
    public $note;

    public function __construct($reportable, $note = null)
    {
        $this->action = $action = 'delete';

        $this->user = null;
        $this->participants = [$reportable->user];

        switch (get_class($reportable)) {
            case Video::class:
                $model = [
                    'id' => $reportable->id,
                    'type' => 'video'
                ];
                break;

            case Comment::class:
                $model = [
                    'id' => $reportable->id,
                    'type' => 'comment'
                ];
                break;

            default:
                $model = [];
                break;
        };

        $this->model = $model;
        $file = new UserFileResource(File::find(1));

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
