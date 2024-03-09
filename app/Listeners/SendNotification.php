<?php

namespace App\Listeners;

use App\Notifications\AppNotification;
use App\Notifications\MessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendNotification 
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
        $users = collect($event->participants)->filter(fn ($participant) => $participant);
        $data = $event->data;

        Notification::send($users, new AppNotification($data));
    }
}
