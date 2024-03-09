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
    CourseSubscribtion,
    File
};

class UserSubscribedCourse
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $participants;
    public $action;
    public $course;
    public $model;

    public $data;

    public function __construct(public CourseSubscribtion $course_subscribtion, $note = null)
    {
        $course = $course_subscribtion->course;
        $this->action = $action = 'course_subscribtion';

        $this->user = $course_subscribtion->user;
        $this->participants = [$course->video?->user];

        $model = [
            'id' => $course->video?->id,
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
