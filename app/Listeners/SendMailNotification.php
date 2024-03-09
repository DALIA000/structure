<?php

namespace App\Listeners;

use App\Notifications\EmailNotification;
use App\Notifications\MessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SendMailNotification 
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

        foreach ($users as $user) {
            $name = $user->user?->business_name ?: $user->user?->fisrt_name . ' ' . $user->user?->last_name;

            switch ($event->action) {
                case 'like':
                    $message = '@' . $event->user?->username . ' has liked your ' . $event->model['type'] . '!';
                    break;

                case 'comment':
                    $message = '@' . $event->user?->username . ' has commentd on your ' . $event->model['type'] . '!';
                    break;

                case 'follow':
                    $message = '@' . $event->user?->username . ' has followed you!';
                    break;

                case 'follow_request':
                    $message = '@' . $event->user?->username . ' has requested to follow you!';
                    break;

                case 'video_tag':
                    $message = '@' . $event->user?->username . ' has tagged you on their video!';
                    break;
                
                default:
                    $message = null;
                    break;
            }
            // from users
            if ($event->user) {
                $mail = (new MailMessage)
                        ->subject(config('app.name'))
                        ->greeting('Hello, ' . $name)
                        ->line($message)
                        ->line('Thank you for using ' . config('app.name'));
            } 
            
            // from admin
            else {
                $mail = $event->mail;
            }

            $is_emailable = $user->user_preferences()->whereHas('preference', function ($query) {
                $query->where('slug', 'email-notifications');
            })->first()?->value;

            if ($is_emailable) {
                Notification::send($users, new EmailNotification($mail));
            }
        }
    }
}
