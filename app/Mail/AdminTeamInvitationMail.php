<?php

namespace App\Mail;

use App\Models\AdminTeamMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminTeamInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AdminTeamMember $member,
        public string $plainPassword,
    ) {}

    public function build()
    {
        return $this->subject('Your Mali Setu admin team account')
            ->view('emails.admin-team-invitation');
    }
}
