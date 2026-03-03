<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCanceledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Model $booking,
        protected string $bookingType,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Booking Canceled — {$this->bookingType}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your {$this->bookingType} booking has been canceled.")
            ->line("**Quantity:** {$this->booking->quantity}")
            ->line("**Total:** MVR " . number_format($this->booking->total_price, 2))
            ->line('If this was a mistake, you can rebook from our website.')
            ->action('Browse Offerings', url('/'))
            ->line('We hope to see you at Horror Bark soon!');
    }
}
