<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Date extends Model
{
    protected $fillable = ['date', 'max_people'];

    public function bookingSchedules(): HasMany
    {
        return $this->hasMany(BookingSchedule::class, 'date_id', 'id');
    }

    public function timeSlots()
    {
        return $this->hasManyThrough(
            TimeSlot::class,          // Final model
            BookingSchedule::class,   // Intermediate model
            'date_id',                // FK on booking_schedules to dates.id
            'id',                     // PK on time_slots
            'id',                     // Local key on dates
            'time_slot_id'            // FK on booking_schedules to time_slots.id
        );
    }
}
