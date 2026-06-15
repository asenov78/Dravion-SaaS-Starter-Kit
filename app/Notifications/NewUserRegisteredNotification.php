<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly User $newUser) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => __('notifications.new_user_title'),
            'body'  => __('notifications.new_user_body', ['name' => $this->newUser->name, 'email' => $this->newUser->email]),
            'url'   => '/admin/users/' . $this->newUser->id . '/edit',
        ];
    }
}