<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConferenceApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $conferenceName;
    public $email;
    public $password;
    public $loginUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($conferenceName, $email, $password)
    {
        $this->conferenceName = $conferenceName;
        $this->email = $email;
        $this->password = $password;
        $this->loginUrl = route('committee.login');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Conference Request has been Approved - PaperFlo')
            ->view('emails.conference_approved');
    }
}
