<?php

namespace App\Models;

use App\Events\BookingStatusUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = ['user_id', 'status_id', 'no_of_people', 'time_slot_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(BookingStatus::class, 'status_id', 'id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot_id', 'id');
    }

    protected $dispatchesEvents = [
        'saved' => BookingStatusUpdated::class,
    ];
}
