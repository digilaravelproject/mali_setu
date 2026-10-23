<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $newPassword;     // nullable; only set if you insist on sending it

    /**
     * @param  \App\Models\User  $user
     * @param  string|null       $newPassword  // pass null to avoid emailing it
     */
    public function __construct($user, $newPassword = null)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    public function build()
    {
        return $this->subject('Your password was changed')
            ->view('emails.password_changed')
            ->with([
                'name'        => $this->user->name,
                'newPassword' => $this->newPassword, // may be null
                'appName'     => config('app.name'),
            ]);
    }
}
