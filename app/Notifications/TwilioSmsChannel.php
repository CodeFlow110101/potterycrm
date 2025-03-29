<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioSmsChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification): void
    {
        $message = $notification->toTwilioSms($notifiable);

        $phoneno = null;
        if ($notifiable instanceof User) {
            $phoneno = $notifiable->phoneno;
        } elseif (is_string($notifiable)) {
            $phoneno = $notifiable;
        } else {
            return;
        }

        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));

        env('APP_ENV') == 'production' && $twilio->messages->create(env('TWILIO_PHONE_COUNTRY_CODE') . $phoneno, [
            'from' => env('TWILIO_FROM'),
            'body' => $message,
        ]);

        env('APP_ENV') == 'local' && Log::info($message);
    }
}
