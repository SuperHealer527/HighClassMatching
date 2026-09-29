<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MatchingActivityNotification extends Notification
{
    use Queueable;

    public string $title;

    public string $message;

    public string $url;

    public bool $forceMail;

    public function __construct(string $title, string $message, string $url = '/dashboard', bool $forceMail = false)
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->forceMail = $forceMail;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        if ($this->forceMail || config('matching.email_notifications')) {
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
