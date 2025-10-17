<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
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
        return $notifiable->preferredNotificationChannels();
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('messages.notifications.account_activation.mail.subject'))
            ->greeting(__('messages.notifications.account_activation.mail.greeting', ['name' => $notifiable->name]))
            ->line(__('messages.notifications.account_activation.mail.line1'))
            ->action(
                __('messages.notifications.account_activation.mail.action'),
                 url(config("app.url"))
            )
            ->line(__('messages.notifications.account_activation.mail.line2'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "title" => __("messages.notifications.account_activation.database.title"),
            "body" => __("messages.notifications.account_activation.database.body"),
            "actionUrl" => url(config("app.url")),
            "actionLabel" => __("messages.notifications.account_activation.database.actionLabel")
        ];
    }
}
