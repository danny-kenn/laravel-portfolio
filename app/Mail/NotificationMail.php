<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $notification;

    public function __construct(User $user, Notification $notification)
    {
        $this->user = $user;
        $this->notification = $notification;
    }

    public function build()
    {
        return $this->subject($this->notification->title)
                    ->view('emails.notification')
                    ->with([
                        'user' => $this->user,
                        'notification' => $this->notification,
                    ]);
    }
}