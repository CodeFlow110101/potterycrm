<?php

namespace App\Notifications;

use App\Mail\PurchaseMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Purchase extends Notification
{
    use Queueable;

    protected $purchase;
    /**
     * Create a new notification instance.
     */
    public function __construct($purchase)
    {
        $this->purchase = $purchase;
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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable)
    {
        return (new PurchaseMail($this->purchase))->to($notifiable->email);
    }

    public function toTwilioSms($notifiable)
    {
        return config('constants.purchase-message');
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
