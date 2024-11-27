<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    protected $table = "postal_codes";

    protected $fillable = ['postcode', 'place', 'state', 'state_code'];
}
