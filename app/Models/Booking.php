<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Observers\BookingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([BookingObserver::class])]
class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = ['user_id', 'status_id', 'no_of_people', 'booking_datetime', 'time_slot_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(BookingStatus::class, 'status_id', 'id');
    }
}
