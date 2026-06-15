<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountSuspendedNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('mail.suspended_subject', ['app' => config('app.name')]))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line(__('mail.suspended_line1'))
            ->line(__('mail.suspended_line2'))
            ->salutation(config('app.name') . ' Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => __('notifications.suspended_title'),
            'body'  => __('notifications.suspended_body'),
            'url'   => '/dashboard',
        ];
    }
}