<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    protected $fillable = ['start_time', 'end_time'];

    public function dates()
    {
        return $this->hasManyThrough(
            Date::class,            // Final model
            BookingSchedule::class, // Intermediate model
            'time_slot_id',         // FK on booking_schedules table pointing to time_slots
            'id',                   // PK on dates table
            'id',                   // PK on time_slots table
            'date_id',               // FK on booking_schedules table pointing to dates
        );
    }

    public function bookings()
    {
        return $this->hasManyThrough(
            Booking::class,         // Final model
            BookingSchedule::class, // Intermediate model
            'time_slot_id',         // FK on booking_schedules pointing to time_slots
            'booking_schedule_id',  // FK on bookings pointing to booking_schedules
            'id',                   // PK on time_slots
            'id'                    // PK on booking_schedules
        );
    }

    protected function timeSlot(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => Carbon::parse($attributes['start_time'])->format('h:i A') . ' - ' . Carbon::parse($attributes['end_time'])->format('h:i A'),
        );
    }

    public function packages()
    {
        return $this->hasManyThrough(
            Package::class,
            PackageTimeSlot::class,
            'time_slot_id',   // Foreign key on package_time_slot
            'id',             // Foreign key on packages
            'id',             // Local key on time_slots
            'package_id'      // Local key on package_time_slot
        );
    }

    public function bookingSchedules(): HasMany
    {
        return $this->hasMany(BookingSchedule::class, 'time_slot_id', 'id');
    }
}
