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
    Competition,
    File,
};

class CompetitionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $participants;
    public $action;
    public $model;

    public $data;

    public function __construct(public Competition $competition, $note = null)
    {
         $this->action = $action = 'competition_created';

        switch ($competition->type) {
            case 0: //
                // private - club subscribers
                $subscribers = $competition->user?->user?->subscribers()->with(['user'])->get()->map(function ($subscribtion) {
                    return $subscribtion->user;
                });
                break;

            case 1: 
                // public - competition subscribers
                $subscribers = $competition->subscribers->map(function ($subscriber) {
                    return $subscriber;
                });
                break;
            
            default:
                $subscribers = $competition->user?->user?->subscribers()->with(['user'])->get()->map(function ($subscribtion) {
                    return $subscribtion->user;
                });
                break;
        }

        $this->user = $competition->user;
        $this->participants = $subscribers; // notifiables

        $model = [
            'id' => $competition->id,
            'type' => 'competition'
        ];

        $this->model = $model;
        $file = $competition->files ? new FileResource($competition->files[0]) : new UserFileResource(File::find(1));

        $this->data = [
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
