<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = "products";

    protected $fillable = ['name', 'description', 'price', 'thumbnail', 'thumbnail_path', 'type_id'];

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'type_id', 'id');
    }
}
