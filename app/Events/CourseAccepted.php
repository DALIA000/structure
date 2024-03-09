<?php

namespace App\Events;

use App\Http\Resources\Dashboard\FileResource;
use App\Http\Resources\Dashboard\UserFileResource;
use App\Models\File;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;
use Illuminate\Notifications\Messages\MailMessage;

class CourseAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $participants;
    public $action;
    public $video;
    public $model;

    public $data;
    public $mail;
    public $payment_url;

    public function __construct(public Invoice $invoice, $note = null)
    {
        $course = $invoice->invoicable;
        $video = $course?->video;
        $this->action = $action = 'course_invoice';

        $this->user = null;
        $this->participants = [$video->user];
        $this->payment_url = url('/api/user/invoices/' . $invoice->id);

        $model = [
            'id' => $video->id,
            'title' => $course->title,
            'type' => 'course',
        ];

        $this->model = $model;
        $file = $course->video->getFirstMedia('cover') ? new FileResource($course->video->getFirstMedia('cover')) : new UserFileResource(File::find(1));

        $this->data = [
            'username' => $this->user?->username,
            'action' => $action,
            'model' => $model,
            'note' => $note,
            'image' => $file,
        ];

        $this->mail = (new MailMessage)
                    ->subject(config('app.name') . ' | ' . 'Course Accepted!')
                    ->greeting('Hello, ' . $video?->user?->fisrt_name . ' ' . $video?->user?->last_name)
                    ->line('Your course "' . $model['title'] . '" has been accepted!')
                    ->line('Your can now proceed to payment to acttivate your course.')
                    ->action('View Invoice', $this->payment_url)
                    ->line('Thank you for using ' . config('app.name'));
    }

    public function broadcastOn()
    {
        return [];
    }
}
