<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
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
            ->subject("Booking Confirmed — {$this->bookingType}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your {$this->bookingType} booking has been confirmed.")
            ->line("**Quantity:** {$this->booking->quantity}")
            ->line("**Total:** MVR " . number_format($this->booking->total_price, 2))
            ->action('View My Bookings', url('/bookings'))
            ->line('Thank you for choosing Horror Bark!');
    }
}
