<?php

namespace App\Events;

use App\Http\Resources\Dashboard\FileResource;
use App\Http\Resources\Dashboard\UserFileResource;
use App\Http\Resources\SessionLivesListResource;
use App\Models\SessionLive;
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
use Illuminate\Notifications\Messages\MailMessage;

class SessionLiveSatrted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $participants;
    public $action;
    public $session;
    public $model;

    public $mail;
    public $data;

    public function __construct(public SessionLive $sessionLive, $note = null)
    {
        $session = $sessionLive->session;
        $this->action = $action = 'sessionLive';

        $this->user = $session?->course?->video?->user;
        $this->participants = $session?->course?->subscribtions()->with(['user'])->get()->pluck('user')->all();

        $model = [
            'id' => $session?->id,
            'type' => 'live'
        ];

        $this->model = $model;
        $file = $this->user?->getFirstMedia() ? new FileResource($this->user?->getFirstMedia()) : new UserFileResource(File::find(1));

        $this->data = [
            'full_name' => $this->user?->user?->business_name ?: "{$this->user?->user?->first_name} {$this->user?->user?->last_name}",
            'username' => $this->user?->username,
            'action' => $action,
            'model' => $model,
            'note' => $note,
            'image' => $file,
        ];

        $this->mail = (new MailMessage)
                    ->subject(config('app.name') . ' | ' . 'Course Accepted!')
                    ->greeting('Hello,')
                    ->line('Course "' . $session->course->title . '" live session has started!')
                    ->line('Thank you for using ' . config('app.name'));
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications');
    }
}
