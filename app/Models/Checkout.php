<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    protected $table = "checkouts";

    protected $fillable = ['user_id', 'coupon_id', 'cart'];
}
