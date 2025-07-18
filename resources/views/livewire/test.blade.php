<?php

use function Livewire\Volt\{state, mount};

use App\Models\Booking;
use App\Notifications\BookingReminder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

$submit = function ($deviceId) {
    dd($deviceId);
};

mount(function () {
    // $booking = Booking::get()->last();
    // $booking->user->notify((new BookingReminder($booking, config('constants.booking-day-before-reminder-message')))->delay(Carbon::now()->addMinutes(2)));
    // dd(Carbon::now()->addHour(2)->format('h:i A'));
});

?>

<div>
    @dump()
</div>

@script
<script>
    FingerprintJS.load().then(fp => {
        fp.get().then(result => {
            $wire.submit(result.visitorId);
        });
    });
</script>
@endscript