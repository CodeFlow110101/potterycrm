<?php

use App\Models\Booking;
use App\Models\BookingSchedule;
use App\Models\Date;
use App\Models\Package;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use function Livewire\Volt\{state, with, mount, rules, updated, computed};

state(['date', 'people' => 0, 'selectedTimeSlots' => [], 'package' => Package::first()?->id]);

rules(fn() => [
    'date' => ['required'],
    'people' => ['required', 'integer', 'min:1'],
]);


with(fn() => [
    'today' => Carbon::today()->format('Y-m-d'),
    'slots' => TimeSlot::whereHas('packages', fn($query) => $query->where('packages.id', $this->package))
        ->when($this->start_date && !$this->end_date, function ($query) {
            return $query->withExists(['bookings' => fn($query) => $query->whereHas('status', fn(Builder $statusQuery) => $statusQuery->where('name', '!=', 'cancel'))->whereHas('date', fn($query) => $query->where('date', $this->start_date))->whereHas('package', fn($query) => $query->where('packages.id', $this->package))])
                ->withCount(['bookings' => fn($query) => $query->whereHas('status', fn(Builder $statusQuery) => $statusQuery->where('name', '!=', 'cancel'))->whereHas('date', fn($query) => $query->where('date', $this->start_date))->whereHas('package', fn($query) => $query->where('packages.id', $this->package))]);
        })
        ->get(),
    'packages' => Package::get(),
]);


updated([
    'date' => fn() => $this->setPeopleAndSelectedTimeSlotsField(),
    'package' => fn() => $this->setPeopleAndSelectedTimeSlotsField(),
]);

$start_date = computed(fn() => Str::of($this->date)->replace('to', "")->explode(" ")->first());
$end_date = computed(fn() => Str::of($this->date)->replace('to', "")->explode(" ")->slice(1)->last());

$setPeopleAndSelectedTimeSlotsField = function () {
    if ($this->start_date && !$this->end_date && Date::where('date', $this->start_date)->exists()) {
        $date = Date::where('date', $this->start_date)->with(['timeSlots' => fn($query) => $query->whereHas('bookingSchedules.package', fn($q) => $q->where('packages.id', $this->package))])->first();
        $this->people = $date->max_people;
        $this->selectedTimeSlots = $date->timeSlots->pluck('id')->all();
    } else {
        $this->people = 0;
        $this->selectedTimeSlots = [];
    }
};

$toggleTimeSlot = function ($slot) {
    $this->selectedTimeSlots = in_array($slot, $this->selectedTimeSlots) ? array_values(array_diff($this->selectedTimeSlots, [$slot])) : array_merge($this->selectedTimeSlots, [$slot]);
};

$submit = function () {

    $this->validate();

    collect([])->pipe(function () {
        if ($this->start_date && $this->end_date) {
            return collect(CarbonPeriod::create(Carbon::parse($this->start_date), $this->end_date))->map(fn($date) => $date->toDateString());
        } else {
            return collect($this->start_date);
        }
    })->each(function ($date) {
        $date = Date::updateOrCreate(
            ['date' => $date],
            ['max_people' => $this->people]
        );

        $date->bookingSchedules()->whereHas('package', fn($query) => $query->where('packages.id', $this->package))->whereDoesntHave('bookings', fn($query) => $query->whereHas('status', fn($statusQuery) => $statusQuery->where('name', '!=', 'cancel')))->delete();

        $bookedTimeSlots = $date->timeSlots()->whereHas('bookingSchedules.package', fn($query) => $query->where('packages.id', $this->package))->whereHas('bookings.status', fn(Builder $query) => $query->where('name', '!=', 'cancel'))->pluck('time_slots.id');
        $timeSlots = collect($this->selectedTimeSlots)->diff($bookedTimeSlots)->values();
        $date->bookingSchedules()->createMany($timeSlots->map(fn($timeSlot) => ['time_slot_id' => $timeSlot, 'package_id' => $this->package]));
    });

    $this->dispatch('show-toastr', message: "Time Slots Updated!");
};

mount(function () {
    if (Date::where('date', Carbon::today()->format('Y-m-d'))->exists()) {
        $date = Date::where('date', Carbon::today()->format('Y-m-d'))->with(['timeSlots' => fn($query) => $query->whereHas('bookingSchedules.package', fn($q) => $q->where('packages.id', $this->package))])->first();
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
            <form x-data="flatpickrDate('{{ $today }}', null, true)" wire:submit="submit" class="backdrop-blur-xl border border-white rounded-lg p-4 flex flex-col grow gap-4">
                <div class="grow flex gap-12">
                    <div class="flex-1 flex flex-col gap-4">
                        <div>
                            <input type="text" x-ref="dateInput" wire:model.live="date" class="hidden" placeholder="Select a date">
                            <div class=" w-full flex justify-end">
                                <div class="border-y border-l w-full rounded-l-lg flex">
                                    <div class="mx-auto w-4/5 h-4/5 my-auto flex flex-col gap-2">
                                        <div>
                                            <div class="text-2xl" x-text="year('{{$this->start_date}}')"></div>
                                            <div x-text="date('{{$this->start_date}}')"></div>
                                        </div>
                                        @if($this->end_date)
                                        <div>To</div>
                                        <div>
                                            <div class="text-2xl" x-text="year('{{$this->end_date}}')"></div>
                                            <div x-text="date('{{$this->end_date}}')"></div>
                                        </div>
                                        @endif
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
                        <div class="flex flex-col gap-4 bg-white text-black rounded-lg p-4">
                            <div class="text-2xl">Package</div>
                            @foreach($packages as $package)
                            <div @click="$refs.radio{{ $package->id }}.click()" class="border border-black text-black rounded-md w-full flex p-4 gap-4 items-center cursor-pointer">
                                <div>
                                    <input wire:model.live="package" x-ref="radio{{ $package->id }}" type="radio" value="{{ $package->id }}" class="size-4 accent-black">
                                </div>
                                <div class="flex gap-4 flex-1">
                                    <div class="size-24">
                                        <img class="size-full rounded-xl" src="{{ asset('storage/'.$package->image_path) }}">
                                    </div>
                                    <div class="flex-1 flex flex-col">
                                        <div class="border-b border-black w-full text-lg font-semibold">
                                            {{ $package->name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
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
                                    <div wire:key="{{ $slot->id }}" class="relative">
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
                <button type="submit" class="relative text-black py-3 uppercase px-6 mx-auto bg-white rounded-lg tracking-tight">
                    <div wire:target="submit" wire:loading.class="invisible">Submit</div>
                    <div wire:target="submit" wire:loading.class.remove="invisible" class="absolute invisible inset-3 flex justify-center items-center">
                        <svg aria-hidden="true" class="size-full text-transparent animate-spin fill-black" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                        </svg>
                    </div>
                </button>
            </form>
        </div>
    </div>
</div>