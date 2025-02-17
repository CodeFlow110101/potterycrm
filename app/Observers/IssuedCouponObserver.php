<?php

namespace App\Observers;

use App\Events\CouponIssued;
use App\Models\IssuedCoupon;

class IssuedCouponObserver
{
    /**
     * Handle the IssuedCoupon "created" event.
     */
    public function created(IssuedCoupon $issuedCoupon): void
    {
        event(new CouponIssued($issuedCoupon));
    }

    /**
     * Handle the IssuedCoupon "updated" event.
     */
    public function updated(IssuedCoupon $issuedCoupon): void
    {
        //
    }

    /**
     * Handle the IssuedCoupon "deleted" event.
     */
    public function deleted(IssuedCoupon $issuedCoupon): void
    {
        //
    }

    /**
     * Handle the IssuedCoupon "restored" event.
     */
    public function restored(IssuedCoupon $issuedCoupon): void
    {
        //
    }

    /**
     * Handle the IssuedCoupon "force deleted" event.
     */
    public function forceDeleted(IssuedCoupon $issuedCoupon): void
    {
        //
    }
}
