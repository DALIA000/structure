<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class UserAccountVerified extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $model;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(env('APP_NAME') . ' - Account verified successfully!')
                    ->markdown('emails.user_account_verified_email');
    }
}
