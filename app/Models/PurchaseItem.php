<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    protected $table = "purchase_items";

    protected $fillable = ['purchase_id', 'product_id', 'quantity', 'item_id', 'status_id'];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(PurchaseItemStatus::class, 'status_id', 'id');
    }
}
