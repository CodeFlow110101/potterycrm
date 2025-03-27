<?php

namespace App\Notifications;

use App\Mail\BookingCapacityExceededMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\TwilioSmsChannel;
use Illuminate\Support\Str;

class BookingStatus extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable->role_id == 1 ? ['mail'] : [TwilioSmsChannel::class];
    }

    public function toTwilioSms($notifiable)
    {
        return config('constants.booking-' . $this->booking->status_id . '-message');
    }

    public function toMail($notifiable)
    {
        return (new BookingCapacityExceededMail($this->booking, $notifiable))->to($notifiable->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
