<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingSchedule;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startTime = Carbon::createFromTime(0, 0, 0);
        $endTime = Carbon::createFromTime(23, 59, 59);
        $interval = 60;


        $iterations = intdiv($startTime->diffInMinutes($endTime) + $interval, $interval);

        Collection::times($iterations, function ($index) use ($startTime, $interval) {
            $slotStart = $startTime->copy()->addMinutes(($index - 1) * $interval);
            $slotEnd = $slotStart->copy()->addMinutes($interval);

            TimeSlot::firstOrCreate([
                'start_time' => $slotStart->format('H:i:s'),
                'end_time'   => $slotEnd->format('H:i:s'),
            ]);
        });

        BookingSchedule::get()->each(function ($booking) {
            $timeSlot = TimeSlot::where('start_time', $booking->start_time)->where('end_time', $booking->end_time)->first();
            $booking->update([
                'time_slot_id' => $timeSlot->id
            ]);
        });
    }
}
