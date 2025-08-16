<?php

use function Livewire\Volt\{state, mount};

use App\Models\Booking;
use App\Models\BookingSchedule;
use App\Models\Date;
use App\Models\TimeSlot;
use App\Models\User;
use App\Notifications\BookingReminder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

$submit = function ($deviceId) {
    dd($deviceId);
};

mount(function () {
    dd(Date::with('bookingSchedules.timeSlot')->first());
});

?>

<div>
    @dump()
</div>