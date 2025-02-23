<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use Carbon\Carbon;
use Twilio\Rest\Client;

class SmsController extends Controller
{
    public static function generateOtp($phoneno)
    {
        $otp = Otp::create([
            'otp' => rand(100000, 999999),
            'phoneno' => $phoneno,
        ]);
        return $otp;
    }

    public static function verifyOtp($id, $userOtp)
    {
        $otp = Otp::find($id);
        return ($otp->otp == $userOtp) && $otp->created_at->greaterThanOrEqualTo(Carbon::now()->subMinutes(2));
    }
}
