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
            ->subject("Resource Deleted")
            ->greeting("Hello {$notifiable->name}!")
            ->line("We wanted to let you know that your {$this->resourceType} (ID: {$this->resource->id}) has been deleted by our administration team.")
            ->action('Check Your Dashboard', $this->getActionUrl())
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "title" => "Resource Deleted",
            "body" => "Your {$this->resourceType} (ID: {$this->resource->id}) has been deleted by our administration team.",
            "actionUrl" => $this->getActionUrl(),
            "actionLabel" => "Check Your Dashboard"
        ];
    }

    private function getActionUrl(): string
    {
        return url(config("app.url")."/$this->path");
    }
}
