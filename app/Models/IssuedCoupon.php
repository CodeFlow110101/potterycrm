<?php

namespace App\Models;

use App\Events\CouponIssued;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssuedCoupon extends Model
{
    protected $table = "issued_coupons";

    protected $fillable = ["coupon_id", "user_id", "is_used", "used_at"];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    protected $dispatchesEvents = [
        'created' => CouponIssued::class,
    ];
}
