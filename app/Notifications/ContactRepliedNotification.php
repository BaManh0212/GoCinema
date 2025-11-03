<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContactRepliedNotification extends Notification
{
    use Queueable;

    protected $reply;

    public function __construct($reply)
    {
        $this->reply = $reply;
    }

    public function via($notifiable)
    {
        return ['database'];  // only database, no email
    }

    public function toArray($notifiable)
    {
        return [
            'contact_id' => $this->reply->contact_id,
            'reply_id' => $this->reply->id,
            'message' => $this->reply->reply_message,
            'admin' => $this->reply->admin->name ?? 'Admin',
        ];
    }
}
