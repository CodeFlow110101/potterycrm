<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSlot extends Model
{
    protected $fillable = ['date_id', 'start_time', 'end_time'];

    public function date(): BelongsTo
    {
        return $this->belongsTo(Date::class, 'date_id', 'id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'time_slot_id', 'id');
    }
}
