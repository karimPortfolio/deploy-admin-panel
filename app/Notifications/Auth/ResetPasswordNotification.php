<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected readonly string $token)
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
        return (new MailMessage)
             ->subject('Reset Password Notification')
            ->line('Click the button below to reset your password:')
            ->action('Reset Password', $this->getResetUrl($notifiable))
            ->line('If you did not request a password reset, no further action is required.');
    }

    private function getResetUrl(object $notifiable): string
    {
        $url = url("/auth/reset-password?token={$this->token}&email=" . urlencode($notifiable->getEmailForPasswordReset()));
        return $url;
    }
}
