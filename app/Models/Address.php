<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $table = "addresses";

    protected $fillable = ['user_id', 'name', 'address', 'postcode_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function postcode(): BelongsTo
    {
        return $this->belongsTo(Postcode::class, 'postcode_id', 'id');
    }
}
