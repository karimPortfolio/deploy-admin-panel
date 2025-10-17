<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly string $password)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name');
        return (new MailMessage)
            ->subject(__('messages.notifications.new_user.mail.subject', ['appName' => $appName]))
            ->greeting(__('messages.notifications.new_user.mail.greeting', ['name' => $notifiable->name]))
            ->line(__('messages.notifications.new_user.mail.line1', ['password' => $this->password]))
            ->line(__('messages.notifications.new_user.mail.line2'))
            ->action(__('messages.notifications.new_user.mail.action'), url('/auth/login'))
            ->line(__('messages.notifications.new_user.mail.line3'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
