<?php

namespace App\Notifications;

use App\Mail\BookingMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($booking, $message)
    {
        $this->booking = $booking;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return collect(['administrator', 'staff'])->contains($notifiable->role->name) ? ['mail'] : [TwilioSmsChannel::class];
    }

    public function toTwilioSms($notifiable)
    {
        return Str::of($this->message)->replace('{first name}',  $this->booking->user->first_name)->replace('{time}',  Carbon::createFromTimeString($this->booking->timeSlot->start_time)->format('h:i A'));
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new BookingMail($this->booking, $this->message))->to($notifiable->email);
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
