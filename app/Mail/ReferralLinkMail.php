<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReferralLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $sender,
        public string $referralLink,
        public ?string $recipientName = null,
        public ?string $personalMessage = null,
    ) {}

    public function build(): self
    {
        return $this->subject($this->sender->name.' invited you to join Mali Setu')
            ->view('emails.referral-link');
    }
}
