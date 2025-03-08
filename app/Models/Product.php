<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Square\Models\Order;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;

    protected $table = "products";

    protected $fillable = ['name', 'description', 'price', 'thumbnail', 'thumbnail_path', 'type_id'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'type_id', 'id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'product_id', 'id');
    }
}
