<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    protected $table = "payment_gateways";

    protected $fillable = ['name'];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'gateway_id', 'id');
    }
}
