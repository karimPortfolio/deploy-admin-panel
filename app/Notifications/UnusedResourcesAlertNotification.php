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
            ->subject("Weekly Audit – Review Unused Resources")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your weekly analytics report on unused resources is ready.")
            ->line("This week, a total of {$this->getTotalUnusedResources()} unused resources were identified:")
            ->line("- {$this->data['unused_security_groups_count']} unused Security Groups found.")
            ->line("- {$this->data['unused_sshkeys_count']} unused SSH Keys found.")
            ->action('Check Your Dashboard', url(config("app.url")))
            ->line("Thank you for using " . config('app.name') . ".");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "title" => "Unused Resources Detected",
            "body" => "This week, a total of {$this->getTotalUnusedResources()} unused resources were identified.",
            "actionUrl" => url(config("app.url")),
            "actionLabel" => "Check Your Dashboard"
        ];
    }

    private function getTotalUnusedResources(): int
    {
        return $this->data['unused_security_groups_count'] + $this->data['unused_sshkeys_count'];
    }

}
