<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = ['name', 'description', 'image', 'image_path'];

    public function packageTimeSlots(): HasMany
    {
        return $this->hasMany(PackageTimeSlot::class, 'package_id', 'id');
    }

    public function timeSlots()
    {
        return $this->hasManyThrough(
            TimeSlot::class,
            PackageTimeSlot::class,
            'package_id',    // Foreign key on package_time_slot
            'id',            // Foreign key on time_slots
            'id',            // Local key on packages
            'time_slot_id'   // Local key on package_time_slot
        );
    }

    public function dates()
    {
        return $this->hasManyThrough(
            Date::class,
            BookingSchedule::class,
            'package_id',    // Foreign key on package_time_slot
            'id',            // Foreign key on time_slots
            'id',            // Local key on packages
            'date_id'   // Local key on package_time_slot
        );
    }

    public function bookingSchedules(): HasMany
    {
        return $this->hasMany(BookingSchedule::class, 'package_id', 'id');
    }
}
