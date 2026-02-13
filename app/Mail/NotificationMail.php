<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The notification instance.
     *
     * @var \App\Models\Notification
     */
    public $notification;

    /**
     * Optional extra data for the template.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct(Notification $notification, array $data = [])
    {
        $this->notification = $notification;
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->notification->title ?: (config('app.name') . ' Notification');

        return $this->subject($subject)
            ->view('emails.notification')
            ->with(array_merge([
                'notification' => $this->notification,
                'subject' => $subject,
                'userName' => optional($this->notification->user)->name,
                'actionUrl' => $this->notification->action_url,
            ], $this->data));
    }
}


