<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Events\BookingStatusUpdated;
use App\Models\Coupon;
use App\Notifications\BookingStatus;
use App\Models\User;
use App\Notifications\BookingReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Events\Dispatcher;

class BookingEventSubscriber implements ShouldQueue
{
    /**
     * Create the event listener.
     */

    public function __construct()
    {
        //
    }
    public function handleBookingCreated(BookingCreated $event): void
    {
        Notification::send(User::where('role_id', 1)->get(), new BookingStatus($event->booking, 'created'));

        $dayBeforeBookingDay = Carbon::parse($event->booking->timeSlot->date->date . ' ' . $event->booking->timeSlot->start_time)->subDay();
        $threeHoursBeforeBookingTime = Carbon::parse($event->booking->timeSlot->start_time)->subHours(3);

        $dayBeforeBookingDay->isFuture() && $event->booking->user->notify((new BookingReminder($event->booking, config('constants.booking-day-before-reminder-message')))->delay($dayBeforeBookingDay));
        $threeHoursBeforeBookingTime->isFuture() && $event->booking->user->notify((new BookingReminder($event->booking, config('constants.booking-three-hour-before-reminder-message')))->delay($dayBeforeBookingDay));
    }

    public function handleBookingStatusUpdated(BookingStatusUpdated $event): void
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

        $event->booking->status_id == 3 && $event->booking->user->notify((new BookingReminder($event->booking, config('constants.booking-day-after-reminder-message')))->delay(Carbon::now()->addDay()));

        $event->booking->user->notify(new BookingStatus($event->booking, 'saved'));
    }

    /**
     * Handle the event.
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            BookingCreated::class,
            [BookingEventSubscriber::class, 'handleUserLogin']
        );

        $events->listen(
            BookingStatusUpdated::class,
            [BookingEventSubscriber::class, 'handleUserLogout']
        );
    }
}
