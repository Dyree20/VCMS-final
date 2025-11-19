<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $status)
    {
        $this->user = $user;
        $this->status = $status;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Account Status Has Changed')
            ->view('emails.user_status_changed');
    }
}
