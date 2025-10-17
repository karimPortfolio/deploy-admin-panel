<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnusedResourcesAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly array $data)
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
        return $notifiable->preferredNotificationChannels();;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('messages.notifications.unused_resources_alert.mail.subject'))
            ->greeting(__('messages.notifications.unused_resources_alert.mail.greeting', ['name' => $notifiable->name]))
            ->line(__('messages.notifications.unused_resources_alert.mail.line1'))
            ->line(__('messages.notifications.unused_resources_alert.mail.line2', ['totalUnusedResources' => $this->getTotalUnusedResources()]))
            ->line(__('messages.notifications.unused_resources_alert.mail.securityGroupsLine', ['unusedSecurityGroupsCount' => $this->data['unused_security_groups_count']]))
            ->line(__('messages.notifications.unused_resources_alert.mail.sshKeysLine', ['unusedSshKeysCount' => $this->data['unused_sshkeys_count']]))
            ->action(__('messages.notifications.unused_resources_alert.mail.action'), url(config("app.url")))
            ->line(__('messages.notifications.unused_resources_alert.mail.line3', ['appName' => config('app.name')]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "title" => __('messages.notifications.unused_resources_alert.database.title'),
            "body" => __('messages.notifications.unused_resources_alert.database.body', ['totalUnusedResources' => $this->getTotalUnusedResources()]),
            "actionUrl" => url(config("app.url")),
            "actionLabel" => __('messages.notifications.unused_resources_alert.database.actionLabel')
        ];
    }

    private function getTotalUnusedResources(): int
    {
        return $this->data['unused_security_groups_count'] + $this->data['unused_sshkeys_count'];
    }

}
