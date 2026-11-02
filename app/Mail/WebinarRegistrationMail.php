<?php

namespace App\Mail;

use App\Models\Webinar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WebinarRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Webinar $webinar) {}

    public function build()
    {
        return $this->subject('Webinar registration: '.$this->webinar->title)
            ->view('emails.webinar-registration');
    }
}
