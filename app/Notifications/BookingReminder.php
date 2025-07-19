<?php

namespace App\Notifications;

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
        return [TwilioSmsChannel::class];
    }

    public function toTwilioSms($notifiable)
    {
        return Str::of($this->message)->replace('{first name}',  $this->booking->user->first_name)->replace('{time}',  Carbon::createFromTimeString($this->booking->timeSlot->start_time)->format('h:i A'));
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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
