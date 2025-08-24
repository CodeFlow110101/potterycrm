<?php

use App\Models\TimeSlot;
use Carbon\Carbon;

use function Livewire\Volt\{state, with, rules};

state(['modal', 'start_time', 'end_time']);

with(fn() => [
    'timeslots' => TimeSlot::withExists(['bookingSchedules', 'bookings', 'packages'])->get()->map(function ($slot) {
        $slot['is_used'] = $slot->booking_schedules_exists || $slot->bookings_exists || $slot->packages_exists;
        return $slot;
    })
]);

$submit = function () {
    $this->resetValidation();
    if (TimeSlot::where('start_time', $this->start_time)->where('end_time', $this->end_time)->exists()) {
        $this->addError('start_time', 'Timeslot already exists.');
        return;
    }


    TimeSlot::create([
        'start_time' => Carbon::createFromFormat('h:i A', $this->start_time)->format('H:i:s'),
        'end_time' => Carbon::createFromFormat('h:i A', $this->end_time)->format('H:i:s')
    ]);

    $this->toggleModal();
};

$deleteModal = fn($id) => TimeSlot::find($id)->delete();

$toggleModal = function () {
    $this->modal = !$this->modal;

    if ($this->modal) {
        $this->start_time = Carbon::now()->copy()->startOfHour()->format('h:i:A');
        $this->end_time = Carbon::now()->copy()->addHour()->startOfHour()->format('h:i:A');
    } else {
        $this->reset(['start_time', 'end_time']);
    }
};

?>


<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="flex justify-between items-center">
        <div class="text-5xl lg:text-7xl font-avenir-next-bold text-white">TimeSlots</div>
        <button wire:click="toggleModal" class="text-black py-3 max-sm:hidden uppercase px-6 font-normal bg-white rounded-lg tracking-tight w-min whitespace-nowrap">Create TimeSlot</button>
    </div>
    <div class="grow relative whitespace-nowrap" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-auto hidden-scrollbar absolute inset-x-0 border border-white rounded-lg" :style="'height: ' + height + 'px;'">
            <table class="w-full overflow-y-hidden backdrop-blur-xl table-auto">
                <thead class="sticky top-0 text-black rounded-t-lg z-10">
                    <tr class="bg-white *:p-3">
                        <th class="font-normal sticky left-0 bg-white">
                            #
                        </th>
                        <th class="font-normal w-full">
                            Time
                        </th>
                        <th class="font-normal">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeslots as $timeslot)
                    <tr class="hover:bg-black/10 transition-colors duration-200 text-white *:p-3">
                        <td class="text-center font-normal sticky left-0 bg-white text-black">{{$loop->iteration}}</td>
                        <td class="text-center font-normal w-full">{{$timeslot->timeSlot}}</td>
                        @if(!$timeslot->is_used)
                        <td class="text-center font-normal">
                            <button wire:click="deleteModal({{ $timeslot->id }})">
                                <svg class="size-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                </svg>
                            </button>
                        </td>
                        @else
                        <td class="text-center font-normal w-full">Used</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($modal)
    <div class="fixed inset-0">
        <div class="size-full flex flex-col justify-center items-center">
            <div class="flex flex-col">
                <div class="grow flex flex-col relative border-white">
                    <form wire:submit="submit" class="backdrop-blur-3xl bg-black/10 shadow-lg border border-white rounded-lg flex flex-col gap-3 p-4 grow">
                        <div class="flex justify-end items-center">
                            <button type="button" wire:click="toggleModal" class="hover:bg-black/30 rounded-full p-1">
                                <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                </svg>
                            </button>
                        </div>
                        <div class="border border-white"></div>
                        <div class="grow flex flex-col w-full">
                            <div class="flex justify-between grow gap-4">
                                <div class="flex flex-col gap-2">
                                    <div x-data="flatpickrTime('{{ $start_time }}')" class="relative cursor-pointer">
                                        <input readonly x-ref="timefield" wire:model.live="start_time" type="text" value="" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-black/10 backdrop-blur-2xl rounded-lg border-2 outline-none border-white appearance-none peer" placeholder=" " />
                                        <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Start time</label>
                                    </div>
                                    @error('start_time')
                                    <div class="text-white text-sm">{{$message}}</div>
                                    @enderror
                                </div>
                                <div>
                                    <div x-data="flatpickrTime('{{ $end_time }}')" class="relative cursor-pointer">
                                        <input readonly x-ref="timefield" wire:model.live="end_time" type="text" value="" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-black/10 backdrop-blur-2xl rounded-lg border-2 outline-none border-white appearance-none peer" placeholder=" " />
                                        <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">End time</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-full flex justify-center py-2">
                            <button class="text-black py-3 max-sm:hidden uppercase px-6 font-normal bg-white rounded-lg tracking-tight w-min whitespace-nowrap">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>