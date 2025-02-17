<?php

namespace App\Notifications;

use App\Models\Coupon;
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
        return $this->booking->status_id == 1 ? [] : [TwilioSmsChannel::class];
    }

    public function toTwilioSms($notifiable)
    {
        return Str::of(config('constants.booking-' . $this->booking->status_id . '-message'))->replace('{url}', str_replace("booking", "product", config('app.url')) . '/' . $this->booking->id);
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
