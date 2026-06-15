<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UpdateInstalledNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly string $version) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => __('notifications.update_installed_title'),
            'body'  => __('notifications.update_installed_body', ['version' => $this->version]),
            'url'   => '/admin/updates',
        ];
    }
}