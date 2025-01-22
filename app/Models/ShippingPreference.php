<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingPreference extends Model
{
    protected $table = "shipping_preferences";

    protected $fillable = ['name'];
}
