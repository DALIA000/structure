<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\SendVerificationEmailNotification;
use App\Notifications\SendVerificationSMSNotification;
use Illuminate\Support\Facades\Notification;
// use App\Services\SendSMS;

class SendVerificationCodeNotification implements ShouldQueue
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
        $user = $event->user;
        if ($event->via == 'email') {
            Notification::send($user, new SendVerificationEmailNotification($user));
        } elseif ($event->via == 'sms') {
            Notification::send($user, new SendVerificationEmailNotification($user));
            // SendSMS::sms(to: $user->phone, message: 'Your 6-digit code is ' . $user->pin);
            // Notification::send($user, new SendVerificationSMSNotification($user));
        }
    }
}
