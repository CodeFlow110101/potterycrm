<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostCode extends Model
{
    protected $table = "postal_codes";

    protected $fillable = ['postcode', 'place', 'state', 'state_code'];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'postcode_id', 'id');
    }
}
