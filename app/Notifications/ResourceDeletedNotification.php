<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceDeletedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly mixed $resource, public readonly string $resourceType, public readonly string $path)
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
            ->subject(__('messages.notifications.deleted_resource.mail.subject'))
            ->greeting(__('messages.notifications.deleted_resource.mail.greeting', ['name' => $notifiable->name]))
            ->line(__('messages.notifications.deleted_resource.mail.line1', ['resourceType' => $this->resourceType, 'resourceId' => $this->resource->id]))
            ->action(__('messages.notifications.deleted_resource.mail.action'), $this->getActionUrl())
            ->line(__('messages.notifications.deleted_resource.mail.line2'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "title" => __('messages.notifications.deleted_resource.database.title'),
            "body" => __('messages.notifications.deleted_resource.database.body', ['resourceType' => $this->resourceType, 'resourceId' => $this->resource->id]),
            "actionUrl" => $this->getActionUrl(),
            "actionLabel" => __('messages.notifications.deleted_resource.database.actionLabel')
        ];
    }

    private function getActionUrl(): string
    {
        return url(config("app.url")."/$this->path");
    }
}
