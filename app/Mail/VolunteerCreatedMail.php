<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VolunteerCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $temporaryPassword,
        public int $commissionPoints,
    ) {}

    public function build(): self
    {
        return $this->subject('Your Mali Setu volunteer account')
            ->view('emails.volunteer-created');
    }
}
