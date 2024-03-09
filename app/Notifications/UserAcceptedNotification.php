<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserAcceptedMail as Mailable;

class UserAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $user;
    private $message;

    public function __construct($user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new Mailable($notifiable, $this->message))->to($notifiable->email);
    }

    public function toArray($notifiable)
    {
        return [
            'notification_type' => 'users',
            'notification_action' => 'accepted',
            'user_username' => $this->user?->username,
            'notification_message' => $this->message,
        ];
    }
}
