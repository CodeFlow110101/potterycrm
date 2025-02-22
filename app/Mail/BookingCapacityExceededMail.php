<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCapacityExceededMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected $booking;
    protected $user;

    public function __construct($booking, $user)
    {
        $this->booking = $booking;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Capacity Exceeded Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.bookingcapacityexceededmail',
            with: [
                'booking' => $this->booking,
                'user' => $this->user,
                'time_slot' => Carbon::createFromFormat('H:i:s', $this->booking->timeSlot->start_time)->format('h:i A') . ' - ' . Carbon::createFromFormat('H:i:s', $this->booking->timeSlot->end_time)->format('h:i A'),
                'booking_date' =>  Carbon::parse($this->booking->timeSlot->date->date)->format('d M Y'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
