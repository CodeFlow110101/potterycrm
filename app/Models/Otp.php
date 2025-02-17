<?php

namespace App\Models;

use App\Events\ConfirmationCodeGenerated;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $table = "otps";

    protected $fillable = ['otp', 'phoneno'];

    protected $dispatchesEvents = [
        'created' => ConfirmationCodeGenerated::class,
    ];
}
