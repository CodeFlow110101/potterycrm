<?php

namespace App\Listeners;

use App\Events\BookingStatusUpdated;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookingStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBookingStatusNotification
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
    public function handle(BookingStatusUpdated $event): void
    {
        $coupon = null;
        if ($event->booking->status_id == 4) {
            $bookings =  $event->booking->user->bookings()->whereHas('status', function ($query) {
                $query->where('name', 'complete');
            })->get();

            $eligibleCoupons = Coupon::where('status', true)->get()->filter(function ($coupon) use ($bookings) {
                if (($coupon->repeat == false && $coupon->repeat_cycle == $bookings->count()) || ($coupon->repeat == true && $bookings->count() % $coupon->repeat_cycle === 0)) {
                    return $coupon->id;
                }
            })->map(function ($coupon) {
                return [$coupon->id, $coupon->name];
            });

            if ($eligibleCoupons->count() != 0) {
                $coupon = $eligibleCoupons->random();
                $event->booking->user->issuedcoupons()->create([
                    'coupon_id' => $coupon[0],
                    'is_used' => false
                ]);
                $coupon = $coupon[1];
            }
        }

        // if($event->booking->status_id == 1){

        // }

        $admins = User::where('role_id', 1)->get();
        $users = $admins->push($event->booking->user)->unique('id');
        Notification::send($users, new BookingStatus($event->booking));
    }
}
