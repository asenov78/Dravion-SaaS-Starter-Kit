<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivatedNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.activated_subject', ['app' => config('app.name')]))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line(__('mail.activated_line1'))
            ->action('Log In', config('app.url') . '/login')
            ->salutation(config('app.name') . ' Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => __('notifications.activated_title'),
            'body'  => __('notifications.activated_body'),
            'url'   => '/dashboard',
        ];
    }
}