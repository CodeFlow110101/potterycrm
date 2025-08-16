<?php

use App\Models\BookingSchedule;
use App\Models\Date;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use function Livewire\Volt\{state, with, mount, rules, updated};

state(['date', 'people' => 0, 'selectedTimeSlots' => []]);

rules(fn() => [
    'date' => ['required'],
    'people' => ['required', 'integer', 'min:1'],
]);


with(fn() => [
    'today' => Carbon::today()->format('Y-m-d'),
    'slots' => TimeSlot::withExists(['bookings' => fn($query) => $query->whereHas('status', fn(Builder $statusQuery) => $statusQuery->where('name', '!=', 'cancel'))->whereHas('date', fn($query) => $query->where('date', $this->date))])
        ->withCount(['bookings' => fn($query) => $query->whereHas('status', fn(Builder $statusQuery) => $statusQuery->where('name', '!=', 'cancel'))->whereHas('date', fn($query) => $query->where('date', $this->date))])
        ->get()
]);


updated(['date' => function () {
    if (Date::where('date', $this->date)->exists()) {
        $date = Date::where('date', $this->date)->with(['timeSlots'])->first();
        $this->people = $date->max_people;
        $this->selectedTimeSlots = $date->timeSlots->pluck('id')->all();
    } else {
        $this->people = 0;
        $this->selectedTimeSlots = [];
    }
}]);

$toggleTimeSlot = function ($slot) {
    $this->selectedTimeSlots = in_array($slot, $this->selectedTimeSlots) ? array_values(array_diff($this->selectedTimeSlots, [$slot])) : array_merge($this->selectedTimeSlots, [$slot]);
};

$submit = function () {
    $this->validate();

    $date = Date::updateOrCreate(
        ['date' => $this->date],
        ['max_people' => collect($this->selectedTimeSlots)->isEmpty() ? 0 : $this->people]
    );

    $date->bookingSchedules()->whereDoesntHave('bookings', fn($query) => $query->whereHas('status', fn($statusQuery) => $statusQuery->where('name', '!=', 'cancel')))->delete();

    $bookedTimeSlots = $date->timeSlots()->whereHas('bookings.status', fn(Builder $query) => $query->where('name', '!=', 'cancel'))->pluck('time_slots.id');
    $timeSlots = collect($this->selectedTimeSlots)->diff($bookedTimeSlots)->values();
    $date->bookingSchedules()->createMany($timeSlots->map(fn($timeSlot) => ['time_slot_id' => $timeSlot]));

    $this->dispatch('show-toastr', message: "Time Slots Updated!");
};

mount(function () {
    if (Date::where('date', Carbon::today()->format('Y-m-d'))->exists()) {
        $date = Date::where('date', Carbon::today()->format('Y-m-d'))->with(['timeSlots'])->first();
        $this->date = $date->date;
        $this->people = $date->max_people;
        $this->selectedTimeSlots = $date->timeSlots->pluck('id')->all();
    } else {
        $this->date = Carbon::today()->format('Y-m-d');
    }
});

?>

<div class="grow flex flex-col gap-8 py-8 text-white w-11/12 mx-auto">
    <div class="text-7xl font-avenir-next-bold">Manage Bookings</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-y-auto hidden-scrollbar absolute inset-x-0 flex flex-col grow" :style="'height: ' + height + 'px;'">
            <form x-data="flatpickrDate('{{ $today }}', null)" wire:submit="submit" class="backdrop-blur-xl border border-white rounded-lg p-4 flex flex-col grow gap-4">
                <div class="grow flex gap-12">
                    <div class="flex-1">
                        <input type="text" x-ref="dateInput" wire:model.live="date" class="hidden" placeholder="Select a date">
                        <div class=" w-full flex justify-end">
                            <div class="border-y border-l w-full rounded-l-lg flex">
                                <div class="mx-auto w-4/5 h-4/5 my-auto">
                                    <div class="text-2xl" x-text="year('{{$date}}')"></div>
                                    <div x-text="date('{{$date}}')"></div>
                                </div>
                            </div>
                            <div wire:ignore x-ref="calendarContainer" class="flex justify-center"></div>
                        </div>
                        @error('date')
                        <div wire:transition.in.scale.origin.top.duration.1000ms class="text-sm">
                            <span class="error">{{ $message }}</span>
                        </div>
                        @enderror
                    </div>
                    <div class="flex-1 flex flex-col gap-4">
                        <div class="flex flex-col gap-4 bg-white text-black p-4 rounded-lg">
                            <div class="text-2xl" x-text="'No of people'"></div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <input wire:model="people" x-mask="99" @input="if ($event.target.value.trim() === '00' || $event.target.value.trim() === '0') $event.target.value = 1" class="bg-transparent text-xl text-center outline-none border border-black p-4 w-24" x-mask="99" />
                                </div>
                            </div>
                            @error('people')
                            <div wire:transition.in.scale.origin.top.duration.1000ms class="text-sm">
                                <span class="error">{{ $message }}</span>
                            </div>
                            @enderror
                        </div>
                        <div class="flex flex-col border p-4 bg-white gap-4 text-black rounded-lg">
                            <div class="text-2xl" x-text="'Time'"></div>
                            <div>
                                <div class="flex flex-wrap justify-around gap-4 text-sm">
                                    @foreach($slots as $slot)
                                    <div class="relative">
                                        <button type="button" @if(!$slot->bookings_exists) wire:click="toggleTimeSlot('{{$slot->id}}')" @endif class="border peer border-black py-1 px-4 rounded-full @if(in_array($slot->id,$selectedTimeSlots)) @if($slot->bookings_exists) bg-red-700 cursor-not-allowed @else bg-black @endif text-white @endif">{{$slot->timeSlot}}</button>
                                        @if($slot->bookings_exists)
                                        <div class="absolute inset-0 -translate-y-10 invisible peer-hover:visible flex flex-col items-center gap-0">
                                            <div class="text-center bg-black text-white border rounded-lg py-1 w-1/2 ">
                                                {{ $slot->bookings_count }}
                                            </div>
                                            <div class="-mt-3">
                                                <svg class="text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M18.425 10.271C19.499 8.967 18.57 7 16.88 7H7.12c-1.69 0-2.618 1.967-1.544 3.271l4.881 5.927a2 2 0 0 0 3.088 0l4.88-5.927Z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('selectedTimeSlots')
                            <div wire:transition.in.scale.origin.top.duration.1000ms class="text-sm">
                                <span class="error">{{ $message }}</span>
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <button type="submit" class="text-black py-3 uppercase px-6 mx-auto bg-white rounded-lg tracking-tight">Submit</button>
            </form>
        </div>
    </div>
</div>