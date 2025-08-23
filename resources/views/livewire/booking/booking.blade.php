<?php

use App\Events\BookingStatusUpdated;
use App\Http\Controllers\SmsController;
use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\Coupon;
use App\Models\Date;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;

use function Livewire\Volt\{mount, state, with, on};

state(['path', 'modal' => false, 'createBookingModal' => false, 'booking', 'status', 'url', 'auth', 'search', 'status_filter' => 'all', 'booking_from_date' => Carbon::today()->format('d M Y'), 'booking_to_date' => Carbon::today()->format('d M Y')]);

with(fn() => [
    'bookings' => Booking::with(['user', 'status', 'date', 'timeSlot', 'package'])
        ->when(!Gate::allows('view-any-booking'), function ($query) {
            $query->where('user_id', $this->auth->id);
        })->whereHas('user', function (Builder $query) {
            $query->where('email', 'like', '%' . $this->search . '%')
                ->orWhere('first_name', 'like', '%' . $this->search . '%')
                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                ->orWhere('phoneno', 'like', '%' . $this->search . '%');
        })
        ->when(Gate::allows('view-booking-filters'), fn($query) => $query->whereHas('date', fn(Builder $query) => $query->whereBetween('date', [Carbon::createFromFormat('d M Y', $this->booking_from_date)->toDateString(), Carbon::createFromFormat('d M Y', $this->booking_to_date)->toDateString()])))
        ->when($this->status_filter != 'all', function ($query) {
            $query->whereHas('status', function ($query) {
                $query->where('name', $this->status_filter);
            });
        })->has('timeSlot')->latest()->get(),
    'status_filter_options' => BookingStatus::get()
]);

on([
    'reset' => function () {
        $this->reset();
    },
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

$toggleCreateBookingModal = function () {
    $this->createBookingModal = !$this->createBookingModal;
};

$submit = function () {
    $this->modal = !$this->modal;
    $this->booking->update(['status_id' => $this->status]);
    $this->booking->updateQuietly(['status_id' => $this->status]);
};

mount(function ($url, $auth, $path) {
    $this->url = $url;
    $this->auth = $auth;
    $this->path = $path;
});
?>

<div x-data="flatpickrDate(null,null)" class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="flex justify-between items-center">
        <div class="text-5xl lg:text-7xl font-avenir-next-bold text-white">Bookings</div>
        <div class="flex items-center gap-4">
            @can('create-booking')
            <button type="button" wire:click="toggleCreateBookingModal" class="text-black max-sm:hidden py-3 uppercase px-6 font-normal bg-white rounded-lg tracking-tight w-min whitespace-nowrap">Create Booking</button>
            @endcan
            @canany(['create-date','update-date'])
            <a href="/manage-booking" wire:navigate class="text-black max-sm:hidden py-3 uppercase px-6 font-normal bg-white rounded-lg tracking-tight w-min whitespace-nowrap">Manage Booking</a>
            @endcanany
        </div>
    </div>
    @can('view-booking-filters')
    <div class="flex items-center gap-4 w-full max-sm:flex-col *:w-full">
        <div>
            <div class="flex gap-3 items-center px-2.5 py-2.5 w-full text-sm text-white font-semibold backdrop-blur-2xl bg-black/10 rounded-lg border-2 border-white">
                <div>
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input wire:model.live="search" type="text" value="" id="floating_outlined" class="block size-full bg-transparent appearance-none focus:outline-none focus:ring-0 peer placeholder:text-white/70" placeholder="Search" />
            </div>
        </div>
        <div class="flex items-center gap-4 max-sm:flex-col">
            <div class="max-sm:w-full cursor-pointer">
                <div x-data="{ show: false }" @click="show=!show;" @click.away="show=false" class="relative cursor-pointer">
                    <input readonly wire:model.live="status_filter" type="text" value="" id="floating_outlined" class="block cursor-pointer capitalize px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-black/10 backdrop-blur-2xl rounded-lg border-2 outline-none border-white appearance-none peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Status</label>
                    <div x-cloak x-show="show" class="absolute z-50 top-14 block w-full text-sm text-white overflow-clip bg-black/10 backdrop-blur-2xl rounded-lg border-2 outline-none border-white appearance-none peer">
                        <button wire:click="$set('status_filter','all')" class="py-2.5 px-2.5 capitalize hover:bg-black/20 w-full text-start">all</button>
                        @foreach($status_filter_options as $option)
                        <button wire:click="$set('status_filter','{{ $option->name }}')" class="py-2.5 px-2.5 capitalize hover:bg-black/20 w-full text-start">{{ $option->name }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4 w-full *:w-full">
                <div x-data="flatpickrDate(null,null)" class="cursor-pointer">
                    <div class="relative cursor-pointer">
                        <input readonly x-ref="dateInput" wire:model.live="booking_from_date" type="text" value="" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-black/10 backdrop-blur-2xl rounded-lg border-2 outline-none border-white appearance-none peer" placeholder=" " />
                        <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Booking From</label>
                    </div>
                </div>
                <div x-data="flatpickrDate(null,null)" class="cursor-pointer">
                    <div class="relative cursor-pointer">
                        <input readonly x-ref="dateInput" wire:model.live="booking_to_date" type="text" value="" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-black/10 backdrop-blur-2xl rounded-lg border-2 outline-none border-white appearance-none peer" placeholder=" " />
                        <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Booking To</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    <div class="grow relative whitespace-nowrap border border-white rounded-lg overflow-hidden" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-auto hidden-scrollbar absolute inset-0" :style="'height: ' + height + 'px;'">
            <table class="w-full backdrop-blur-xl">
                <thead class="bg-white text-black sticky top-0 z-10">
                    <tr class="bg-white *:p-3">
                        <th class="font-normal sticky left-0 bg-white">
                            #
                        </th>
                        @can('view-customer-detail-columns-booking')
                        <th class="font-normal">
                            First Name
                        </th>
                        <th class="font-normal">
                            Last Name
                        </th>
                        <th class="font-normal">
                            Phone no
                        </th>
                        <th class="font-normal">
                            Email
                        </th>
                        @endcan
                        <th class="font-normal">
                            No of People
                        </th>
                        <th class="font-normal">
                            Booked on
                        </th>
                        <th class="font-normal">
                            Booking Date
                        </th>
                        <th class="font-normal">
                            Time Slot
                        </th>
                        <th class="font-normal">
                            Package
                        </th>
                        <th class="font-normal">
                            Status
                        </th>
                        @can('update-booking')
                        <th class="font-normal max-sm:hidden">
                            Acton
                        </th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr class="hover:bg-black/10 transition-colors duration-200 text-white *:p-3">
                        <td class="text-center font-normal sticky left-0 bg-white text-black">{{$loop->iteration}}</td>
                        @can('view-customer-detail-columns-booking')
                        <td class="text-center font-normal">{{$booking->user->first_name}}</td>
                        <td class="text-center font-normal">{{$booking->user->last_name}}</td>
                        <td class="text-center font-normal">{{$booking->user->phoneno}}</td>
                        <td class="text-center font-normal">{{$booking->user->email}}</td>
                        @endcan
                        <td class="text-center font-normal">{{$booking->no_of_people}}</td>
                        <td class="text-center font-normal">{{Carbon::parse($booking->created_at)->format('d M Y')}}</td>
                        <td class="text-center font-normal">{{Carbon::parse($booking->date->date)->format('d M Y')}}</td>
                        <td class="text-center font-normal">{{ $booking->timeSlot->timeSlot }}</td>
                        <td class="text-center font-normal">{{ $booking->package->name }}</td>
                        <td class="text-center font-normal capitalize">{{$booking->status->name}}</td>
                        @can('update-booking')
                        <td class="text-center font-normal capitalize flex justify-center max-sm:hidden">
                            <button wire:click="toggleModal({{ $booking->id }})">
                                <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                </svg>
                            </button>
                        </td>
                        @endcan
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($createBookingModal)
    <div class="fixed inset-0 flex justify-center items-center z-10">
        <div class="size-5/6 flex flex-col bg-black/10 rounded-lg">
            <livewire:form.booking-form :path="$path" />
        </div>
    </div>
    @endif

    @if($modal)
    <div class="fixed inset-0 flex justify-center items-center z-10">
        <form wire:submit="submit" class="w-1/2 backdrop-blur-3xl shadow-lg border border-white rounded-lg flex flex-col gap-3 p-4">
            <div class="flex justify-end items-center">
                <button type="button" wire:click="toggleModal" class="hover:bg-black/30 rounded-full p-1">
                    <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                </button>
            </div>
            <div class="border border-white"></div>
            <div class="grow">
                <div class="border border-white rounded-lg flex items-center justify-center p-2 py-24 h-full">
                    <div class="flex justify-stretch items-center w-11/12">
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.status = 1;" :class="[1 , 2 , 3 , 4].includes(Number($wire.status)) ? 'bg-white text-black border-white' : 'hover:bg-white hover:text-black hover:border-white transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">1</button>
                            <div :class="[1 , 2 , 3 , 4].includes(Number($wire.status)) ? 'text-white' : 'text-black/30'" class="absolute pointer-events-none -translate-y-full pb-2 font-semibold text-center"><span class="whitespace-nowrap">Open</div>
                        </div>
                        <div class="w-full h-1.5 bg-black/30 relative overflow-hidden">
                            <div class="absolute inset-0">
                                <div :class="[2 , 3 , 4].includes(Number($wire.status))  ? 'w-full' : 'w-0'" class="h-full bg-white transition-all duration-200"></div>
                            </div>
                        </div>
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.status = 2;" :class="[2 , 3 , 4].includes(Number($wire.status)) ? 'bg-white text-black border-white' : 'hover:bg-white hover:text-black hover:border-white transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">2</button>
                            <div :class="[2 , 3 , 4].includes(Number($wire.status)) ? 'text-white' : 'text-black/30'" class="absolute pointer-events-none translate-y-full pt-2 font-semibold text-center"><span class="whitespace-nowrap">Confirm</div>
                        </div>
                        <div class="w-full h-1.5 bg-black/30 relative overflow-hidden">
                            <div class="absolute inset-0">
                                <div :class="[3 , 4].includes(Number($wire.status))  ? 'w-full' : 'w-0'" class="h-full bg-white transition-all duration-200"></div>
                            </div>
                        </div>
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.status = 3;" :class="[3 , 4].includes(Number($wire.status)) ? 'bg-white text-black border-white' : 'hover:bg-white hover:text-black hover:border-white transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">3</button>
                            <div :class="[3 , 4].includes(Number($wire.status)) ? 'text-white' : 'text-black/30'" class="absolute pointer-events-none -translate-y-full pb-2 font-semibold text-center"><span class="whitespace-nowrap">Active</div>
                        </div>
                        <div class="w-full h-1.5 bg-black/30 relative overflow-hidden">
                            <div class="absolute inset-0">
                                <div :class="[4].includes(Number($wire.status))  ? 'w-full' : 'w-0'" class="h-full bg-white transition-all duration-200"></div>
                            </div>
                        </div>
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.status = 4;" :class="[4].includes(Number($wire.status)) ? 'bg-white text-black border-white' : 'hover:bg-white hover:text-black hover:border-white transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">4</button>
                            <div :class="[4].includes(Number($wire.status)) ? 'text-white' : 'text-black/30'" class="absolute pointer-events-none translate-y-full pt-2 font-semibold text-center"><span class="whitespace-nowrap">Complete</div>
                        </div>
                    </div>
                </div>
                @error('status')
                <div class="text-red-700 text-sm">{{$message}}</div>
                @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="relative">
                    <input disabled type="text" value="{{ $booking->user->first_name.' '.$booking->user->first_name }}" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-2 border-white appearance-none focus:outline-none focus:ring-0 focus:border-primary peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Name</label>
                </div>
                <div class="relative">
                    <input disabled type="text" value="{{ $booking->user->phoneno }}" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-2 border-white appearance-none focus:outline-none focus:ring-0 focus:border-primary peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Phone No</label>
                </div>
                <div class="relative">
                    <input disabled type="text" value="{{ $booking->no_of_people }}" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-2 border-white appearance-none focus:outline-none focus:ring-0 focus:border-primary peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">No of People</label>
                </div>
                <div class="relative">
                    <input disabled type="text" value="{{ Carbon::parse($booking->date->date)->format('d M Y') }}" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-2 border-white appearance-none focus:outline-none focus:ring-0 focus:border-primary peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Booking Data</label>
                </div>
                <div class="relative">
                    <input disabled type="text" value="{{ $booking->status->name }}" id="floating_outlined" class="capitalize block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-2 border-white appearance-none focus:outline-none focus:ring-0 focus:border-primary peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-primary peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Status</label>
                </div>
                <button @click="$wire.status = 5;" type="button" class="text-black py-3 uppercase bg-white rounded-lg tracking-tight">Cancel Booking</button>
            </div>
            <div class="flex justify-around gap-4">
                <button type="submit" wire:loading.class="pointer-events-none" wire:dirty.class.remove="pointer-events-none opacity-50" class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight relative">
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