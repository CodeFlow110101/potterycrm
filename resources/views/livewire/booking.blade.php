<?php

use App\Http\Controllers\SmsController;
use App\Models\Booking;
use App\Models\BookingStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

use function Livewire\Volt\{mount, state, with, on};

state(['modal' => false, 'booking', 'status', 'url']);

with(fn() => ['bookings' => Booking::with(['user', 'status'])->get()]);

on([
    'reset' => function () {
        $this->reset();
    }
]);

$toggleModal = function ($booking = null) {
    $this->modal = !$this->modal;
    if ($booking) {
        $this->booking = Booking::with(['user', 'status'])->find($booking);
        $this->status = $this->booking->status_id;
    } else {
        $this->reset(['booking', 'status']);
    }
};

$submit = function () {
    $this->modal = !$this->modal;
    $this->booking->update(['status_id' => $this->status]);
    if ($this->status == 2) {
        App::call([SmsController::class, 'send'], ['phoneno' => env('TWILIO_PHONE_COUNTRY_CODE') . $this->booking->user->phoneno, 'message' => 'Your booking has been successfully confirmed!']);
    } else if ($this->status == 3) {
        App::call([SmsController::class, 'send'], ['phoneno' => env('TWILIO_PHONE_COUNTRY_CODE') . $this->booking->user->phoneno, 'message' => 'Your booking is now active!' . 'You can start selecting items through this link:- ' . str_replace("booking", "product", $this->url) . '/' . $this->booking->id]);
    } else if ($this->status == 4) {
    }
};

mount(function ($url) {
    $this->url = $url;
});
?>

<div class="grow flex flex-col">
    <div class="w-full p-6">
        <div class="text-black/60 font-semibold text-xl border border-black/30 rounded-full w-min whitespace-nowrap py-2 px-4">Pending Bookings: <span class="text-green-500">{{count($bookings)}}</span></div>
    </div>
    <div class="grow w-full px-6 pb-6">
        <div class="rounded-3xl px-6 py-2 font-medium text-black/60 text-lg border border-black/60 h-full">
            <div class="border-b border-black/60 py-4 flex justify-between items-center">
                <div class="text-xl">Bookings</div>
                <div class="relative">
                    <input class="border border-black/30 rounded-full outline-none py-2 pl-10 pr-4">
                    <div class="absolute inset-y-0 flex items-center pl-2">
                        <svg class="w-6 h-6 text-black/30" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="overflow-y-auto h-[70vh] rounded-lg">
                <table class="w-full overflow-y-hidden">
                    <thead class="bg-primary/40">
                        <tr>
                            <th class="font-normal py-2">
                                No
                            </th>
                            <th class="font-normal py-2">
                                First Name
                            </th>
                            <th class="font-normal py-2">
                                Last Name
                            </th>
                            <th class="font-normal py-2">
                                Phone no
                            </th>
                            <th class="font-normal py-2">
                                No of People
                            </th>
                            <th class="font-normal py-2">
                                Booked on
                            </th>
                            <th class="font-normal py-2">
                                Booking Date
                            </th>
                            <th class="font-normal py-2">
                                Status
                            </th>
                            <th class="font-normal py-2">
                                Acton
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr class="hover:bg-black/10 transition-colors duration-200">
                            <td class="text-center font-normal py-3">{{$loop->iteration}}</td>
                            <td class="text-center font-normal py-3">{{$booking->user->first_name}}</td>
                            <td class="text-center font-normal py-3">{{$booking->user->last_name}}</td>
                            <td class="text-center font-normal py-3">{{$booking->user->phoneno}}</td>
                            <td class="text-center font-normal py-3">{{$booking->no_of_people}}</td>
                            <td class="text-center font-normal py-3">{{Carbon::parse($booking->created_at)->format('d M Y')}}</td>
                            <td class="text-center font-normal py-3">{{Carbon::parse($booking->booking_datetime)->format('d M Y')}}</td>
                            <td class="text-center font-normal py-3 capitalize">{{$booking->status->name}}</td>
                            <td class="text-center font-normal py-3 capitalize flex justify-center">
                                <button wire:click="toggleModal({{ $booking->id }})">
                                    <svg class="w-6 h-6 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($this->modal)
    <div class="fixed inset-0 flex justify-center items-center">
        <form wire:submit="submit" class="w-1/2 bg-white shadow-lg border border-black/30 rounded-lg flex flex-col gap-3 p-4">
            <div class="flex justify-end items-center">
                <button type="button" wire:click="toggleModal" class="hover:bg-black rounded-full group p-1">
                    <svg class="w-5 h-5 text-black group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                </button>
            </div>
            <div class="border border-black/30"></div>
            <div class="grow">
                <div class="border border-black/30 rounded-lg flex items-center justify-center p-2 py-24 h-full">
                    <div class="flex justify-stretch items-center w-11/12">
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.status = 1;" :class="[1 , 2 , 3 , 4].includes(Number($wire.status)) ? 'bg-primary text-white border-primary' : 'hover:bg-primary hover:text-white hover:border-primary transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">1</button>
                            <div :class="[1 , 2 , 3 , 4].includes(Number($wire.status)) ? 'text-primary' : 'text-black/30'" class="absolute pointer-events-none -translate-y-full pb-2 font-semibold text-center"><span class="whitespace-nowrap">Open</div>
                        </div>
                        <div class="w-full border h-1.5 bg-black/30 rounded-full relative overflow-hidden">
                            <div class="absolute inset-0">
                                <div :class="[2 , 3 , 4].includes(Number($wire.status))  ? 'w-full' : 'w-0'" class="h-full bg-primary transition-all duration-200"></div>
                            </div>
                        </div>
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.status = 2;" :class="[2 , 3 , 4].includes(Number($wire.status)) ? 'bg-primary text-white border-primary' : 'hover:bg-primary hover:text-white hover:border-primary transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">2</button>
                            <div :class="[2 , 3 , 4].includes(Number($wire.status)) ? 'text-primary' : 'text-black/30'" class="absolute pointer-events-none translate-y-full pt-2 font-semibold text-center"><span class="whitespace-nowrap">Confirm</div>
                        </div>
                        <div class="w-full border h-1.5 bg-black/30 rounded-full relative overflow-hidden">
                            <div class="absolute inset-0">
                                <div :class="[3 , 4].includes(Number($wire.status))  ? 'w-full' : 'w-0'" class="h-full bg-primary transition-all duration-200"></div>
                            </div>
                        </div>
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.status = 3;" :class="[3 , 4].includes(Number($wire.status)) ? 'bg-primary text-white border-primary' : 'hover:bg-primary hover:text-white hover:border-primary transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">3</button>
                            <div :class="[3 , 4].includes(Number($wire.status)) ? 'text-primary' : 'text-black/30'" class="absolute pointer-events-none -translate-y-full pb-2 font-semibold text-center"><span class="whitespace-nowrap">Active</div>
                        </div>
                        <div class="w-full border h-1.5 bg-black/30 rounded-full relative overflow-hidden">
                            <div class="absolute inset-0">
                                <div :class="[4].includes(Number($wire.status))  ? 'w-full' : 'w-0'" class="h-full bg-primary transition-all duration-200"></div>
                            </div>
                        </div>
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.status = 4;" :class="[4].includes(Number($wire.status)) ? 'bg-primary text-white border-primary' : 'hover:bg-primary hover:text-white hover:border-primary transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">4</button>
                            <div :class="[4].includes(Number($wire.status)) ? 'text-primary' : 'text-black/30'" class="absolute pointer-events-none translate-y-full pt-2 font-semibold text-center"><span class="whitespace-nowrap">Complete</div>
                        </div>
                    </div>
                </div>
                @error('status')
                <div class="text-red-700 text-sm">{{$message}}</div>
                @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="relative">
                    <input disabled type="text" value="{{ $booking->user->first_name.' '.$booking->user->first_name }}" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-2 border-primary appearance-none focus:outline-none focus:ring-0 focus:border-primary peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm text-primary duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Name</label>
                </div>
                <div class="relative">
                    <input disabled type="text" value="{{ $booking->user->phoneno }}" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-2 border-primary appearance-none focus:outline-none focus:ring-0 focus:border-primary peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm text-primary duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Phone No</label>
                </div>
                <div class="relative">
                    <input disabled type="text" value="{{ $booking->no_of_people }}" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-2 border-primary appearance-none focus:outline-none focus:ring-0 focus:border-primary peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm text-primary duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">No of People</label>
                </div>
                <div class="relative">
                    <input disabled type="text" value="{{ $booking->booking_datetime }}" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-2 border-primary appearance-none focus:outline-none focus:ring-0 focus:border-primary peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm text-primary duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Floating outlined</label>
                </div>
                <div class="relative">
                    <input disabled type="text" value="{{ $booking->status->name }}" id="floating_outlined" class="capitalize block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-2 border-primary appearance-none focus:outline-none focus:ring-0 focus:border-primary peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm text-primary duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Floating outlined</label>
                </div>
            </div>
            <div class="flex justify-center">
                <button type="submit" wire:loading.class="pointer-events-none" wire:dirty.class.remove="pointer-events-none opacity-50" class="pointer-events-none opacity-50 rounded-md text-center py-2 px-4 bg-primary mx-auto text-white text-xl relative">
                    <div wire:loading.class="invisible" wire:target="submit">Submit</div>
                    <div wire:loading.class.remove="invisible" wire:target="submit" class="absolute inset-0 flex justify-center items-center invisible">
                        <svg aria-hidden="true" class="w-8 h-8 text-transparent animate-spin fill-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                        </svg>
                    </div>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>