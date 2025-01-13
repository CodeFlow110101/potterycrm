<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = "coupons";

    protected $fillable = ['name', 'discount_type', 'discount_value', 'trigger_count', 'validity_days', 'status'];
}
