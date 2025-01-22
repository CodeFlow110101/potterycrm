<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use Carbon\Carbon;
use Twilio\Rest\Client;

class SmsController extends Controller
{
    public static function send($phoneno, $message)
    {
        return;
        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));

        $message = $twilio->messages->create($phoneno, [
            'from' => env('TWILIO_FROM'),
            'body' => $message,
        ]);

        return $message;
    }

    public static function generateOtp()
    {
        $otp = Otp::create(['otp' => rand(100000, 999999)]);
        return $otp;
    }

    public static function verifyOtp($id, $userOtp)
    {
        $otp = Otp::find($id);
        return ($otp->otp == $userOtp) && $otp->created_at->greaterThanOrEqualTo(Carbon::now()->subMinutes(2));
    }
}
