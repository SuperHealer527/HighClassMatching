<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchingActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $url = '/dashboard'
    ) {
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        if (config('matching.email_notifications')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toArray($notifiable)
    {
        return ['title' => $this->title, 'message' => $this->message, 'url' => $this->url];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->title)
            ->greeting($notifiable->name.' 様')
            ->line($this->message)
            ->action('内容を確認する', url($this->url));
    }
}
