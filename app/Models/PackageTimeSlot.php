<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageTimeSlot extends Model
{
    protected $fillable = ['time_slot_id', 'package_id'];
}
