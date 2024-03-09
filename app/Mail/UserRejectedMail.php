<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class UserRejectedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;
    public $message;

    public function __construct(Model $user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function build()
    {
        return $this->subject(env('APP_NAME') . ' - Your account has been rejected!')
                    ->markdown('emails.user_rejected_email', ['message' => $this->message]);
    }
}
