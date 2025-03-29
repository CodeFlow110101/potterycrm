<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = "payments";

    protected $fillable = ['purchase_id', 'payment_id', 'amount', 'source', 'type', 'receipt_url', 'status', 'transaction_id', 'gateway_id'];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'gateway_id', 'id');
    }
}
