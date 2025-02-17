<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Date extends Model
{
    protected $fillable = ['date', 'max_people'];

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class, 'date_id', 'id');
    }
}
