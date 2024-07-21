<?php

namespace Utyemma\LaraNotice\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as LaravelNotification;
use Utyemma\LaraNotice\Notify;

class Notification extends LaravelNotification implements ShouldQueue {
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
            private $channels,
            private Notify $mailMessage
        )
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array {
        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage {
        return $this->mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array {
        return $this->mailMessage->toDatabase();
    }
}
