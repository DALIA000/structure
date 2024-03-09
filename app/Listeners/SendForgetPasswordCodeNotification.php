<?php

namespace App\Listeners;

use App\Events\ForgetPasswordCodeGenerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\ForgetPasswordCodeNotification;
use App\Services\SendSMS;

class SendForgetPasswordCodeNotification implements ShouldQueue
{
    use InteractsWithQueue;

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
     * @param  \App\Events\ForgetPasswordCodeGenerated  $event
     * @return void
     */
    public function handle(ForgetPasswordCodeGenerated $event)
    {
        $user = $event->user;
        if ($event->via == 'email') {
            $user->notify(new ForgetPasswordCodeNotification($user));
        } elseif ($event->via == 'sms') {
            $user->notify(new ForgetPasswordCodeNotification($user));
            // SendSMS::sms(to: $user->phone, message: 'Your 6-digit code is ' . $user->pin);
        }
    }
}
