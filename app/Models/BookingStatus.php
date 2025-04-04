<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingStatus extends Model
{
    protected $table = 'booking_statuses';

    protected $fillable = ['name'];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'status_id', 'id');
    }
}
