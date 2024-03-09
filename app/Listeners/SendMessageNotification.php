<?php

namespace App\Listeners;

use App\Notifications\MessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendMessageNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $users = $event->message->participation?->conversation?->getParticipants()->where('id', '!=', app('loggedinUser')->id);
        Notification::send($users, new MessageNotification($event->message));
    }
}
