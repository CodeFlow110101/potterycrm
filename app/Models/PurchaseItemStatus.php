<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseItemStatus extends Model
{
    protected $table = 'purchase_item_statuses';

    protected $fillable = ['name'];

    public function purchaseitems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'status_id', 'id');
    }
}
