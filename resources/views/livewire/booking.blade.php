<?php

use App\Models\Booking;
use Carbon\Carbon;

use function Livewire\Volt\{mount, state, with, on};

with(fn() => ['bookings' => Booking::with(['user', 'status'])->get()]);

on([
    'reset' => function () {
        $this->reset();
    }
]);
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
                    <thead class="bg-amber-500/40">
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
                            <td class="text-center font-normal py-3 capitalize">{{$booking->status->name}}</td>
                            <td class="text-center font-normal py-3 capitalize flex justify-center">
                                <button @click="$dispatch('show-modal', { name: 'update-status', data: {{$booking->id}} })">
                                    <svg class="w-6 h-6 text-amber-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
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
</div>