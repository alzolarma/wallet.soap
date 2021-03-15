<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmTransaction extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->from('example@example.com')
        // ->view('emails.newuser');
        return $this->view('email.confirm')->with([
                        'token' => $this->token,
                        ]);
    }
}
