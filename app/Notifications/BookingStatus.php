<?php

namespace App\Notifications;

use App\Mail\BookingMail;
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
    protected $event;
    protected $subjectText;

    /**
     * Create a new notification instance.
     */
    public function __construct($booking, $event)
    {
        $this->booking = $booking;
        $this->event = $event;
        $this->subjectText = config('constants.admin-booking-alert-mail-subject-' . $booking->status_id);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $this->event == 'created' ? ['mail'] : [TwilioSmsChannel::class];
    }

    public function toTwilioSms($notifiable)
    {
        return config('constants.booking-' . $this->booking->status_id . '-message');
    }

    public function toMail($notifiable)
    {
        return (new BookingMail($this->booking, $notifiable, $this->subjectText))->to($notifiable->email);
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
