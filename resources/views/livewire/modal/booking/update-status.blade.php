<?php

use App\Http\Controllers\SmsController;
use App\Models\Booking;
use App\Models\BookingStatus;
use Illuminate\Support\Facades\App;

use function Livewire\Volt\{state, mount};

state(['id', 'booking']);

$submit = function () {
    $this->dispatch('show-toastr', type: 'success', message: 'Booking Status Updated');
    
    Booking::find($this->booking->id)->update([
        'status_id' => 2,
    ]);

    App::call([SmsController::class, 'send'], ['phoneno' => env('TWILIO_PHONE_COUNTRY_CODE') . $this->booking->user->phoneno, 'message' => 'Your booking has been successfully confirmed!']);
    $this->dispatch('reset');
    $this->dispatch('hide-modal');
};

mount(function ($data) {
    $this->booking = Booking::with(['user', 'status'])->find($data);
});
?>

<div class="h-full flex flex-col p-6 gap-6">
    <div class="flex justify-between items-center border-b border-black/30 pb-2">
        <div class="text-xl text-black/60 font-semibold">Update Status</div>
        <button @click="$dispatch('hide-modal')" class="size-min p-1 group hover:bg-black/30 rounded-full transition-colors duration-200">
            <svg class="w-6 h-6 text-black/30 group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
            </svg>
        </button>
    </div>
    <div class="grow flex flex-col gap-6 justify-around w-11/12 mx-auto">
        <div class="flex justify-between gap-2 text-black/60">
            <div class="basis-1/3 font-medium">Name:</div>
            <div class="basis-1/3 border border-amber-500 rounded-md p-2">{{$booking->user->first_name}}</div>
            <div class="basis-1/3 border border-amber-500 rounded-md p-2">{{$booking->user->last_name}}</div>
        </div>
        <div class="flex justify-between gap-2 text-black/60">
            <div class="basis-1/3 font-medium">Phone:</div>
            <div class="basis-2/3 border border-amber-500 rounded-md p-2">{{$booking->user->phoneno}}</div>
        </div>
        <div class="flex justify-between gap-2 text-black/60">
            <div class="basis-1/3 font-medium">Email:</div>
            <div class="basis-2/3 border border-amber-500 rounded-md p-2">{{$booking->user->email}}</div>
        </div>
        <div class="flex justify-between gap-2 text-black/60">
            <div class="basis-1/3 font-medium">No of People:</div>
            <div class="basis-2/3 border border-amber-500 rounded-md p-2">{{$booking->no_of_people}}</div>
        </div>
        <div class="flex justify-around">
            <button @click="$dispatch('hide-modal')" class="py-2 px-4 border border-amber-500 rounded-md text-amber-500 font-semibold">Cancel</button>
            <button wire:click="submit" class="py-2 px-4 bg-amber-500 rounded-md text-white font-semibold">Confirm Booking</button>
        </div>
    </div>
</div>