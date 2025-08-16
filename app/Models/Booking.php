<?php

namespace App\Models;

use App\Events\BookingCreated;
use App\Events\BookingStatusUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = ['user_id', 'status_id', 'no_of_people', 'booking_schedule_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(BookingStatus::class, 'status_id', 'id');
    }

    // public function timeSlot(): BelongsTo
    // {
    //     return $this->belongsTo(TimeSlot::class, 'time_slot_id', 'id');
    // }

    public function timeSlot()
    {
        return $this->hasOneThrough(
            TimeSlot::class,        // Final model
            BookingSchedule::class, // Intermediate model
            'id',                   // FK on booking_schedules (id) - local key for bookings
            'id',                   // PK on time_slots
            'booking_schedule_id',         // FK on bookings to booking_schedules.id
            'time_slot_id'          // FK on booking_schedules to time_slots.id
        );
    }

    public function date()
    {
        return $this->hasOneThrough(
            Date::class,        // Final model
            BookingSchedule::class, // Intermediate model
            'id',                   // FK on booking_schedules (id) - local key for bookings
            'id',                   // PK on time_slots
            'booking_schedule_id',         // FK on bookings to booking_schedules.id
            'date_id'          // FK on booking_schedules to time_slots.id
        );
    }

    protected $dispatchesEvents = [
        'created' => BookingCreated::class,
        'saved' => BookingStatusUpdated::class,
    ];
}
