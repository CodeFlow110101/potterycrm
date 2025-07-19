<?php

namespace App\Listeners;

use App\Notifications\CouponIssued as CouponNotification;
use App\Events\CouponIssued;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCouponNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CouponIssued $event): void
    {
        Notification::send($event->issuedCoupon->user, new CouponNotification($event->issuedCoupon));
    }
}
