<?php

namespace App\Mail;

use App\Support\BookingSupport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Model $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: BookingSupport::typeLabel($this->booking).' booking reminder'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bookings.reminder',
            with: [
                'booking' => $this->booking,
                'title' => BookingSupport::title($this->booking),
                'typeLabel' => BookingSupport::typeLabel($this->booking),
                'schedule' => BookingSupport::scheduleLabel($this->booking),
                'invoiceUrl' => BookingSupport::invoiceDownloadUrl($this->booking->invoice),
                'passUrl' => BookingSupport::passDownloadUrl($this->booking),
            ]
        );
    }
}
