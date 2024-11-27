<?php

use function Livewire\Volt\{state};


?>

<div class="grow flex flex-col">
    <div class="grow w-full grid grid-cols-4 p-6">
        <div class="border border-black/60 shadow-lg group hover:bg-black transition-colors duration-300 size-full rounded-xl text-white p-4 flex flex-col justify-around">
            <div>
                <svg class="w-12 h-12 text-black/60 group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16m-8-3V4M7 7V4m10 3V4M5 20h14a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-7h.01v.01H8V13Zm4 0h.01v.01H12V13Zm4 0h.01v.01H16V13Zm-8 4h.01v.01H8V17Zm4 0h.01v.01H12V17Zm4 0h.01v.01H16V17Z" />
                </svg>
            </div>
            <div class="text-2xl text-black/60 group-hover:text-white transition-colors duration-300">Total Bookings</div>
            <div class="text-green-500 text-2xl">23</div>
        </div>
    </div>
    <div class="h-3/5 w-full px-6 pb-6">
        <div class="rounded-3xl px-6 py-2 font-medium text-black/60 text-lg border border-black/60 h-full">
            <div class="border-b border-black/60 py-4">Bookings</div>
            <div class="py-2 overflow-y-auto h-[38vh]">
                <table class="w-full overflow-y-hidden rounded-t-lg">
                    <thead class="bg-black/10">
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
                                No of People
                            </th>
                            <th class="font-normal py-2">
                                Booked on
                            </th>
                            <th class="font-normal py-2">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center font-normal">1</td>
                            <td class="text-center font-normal">Nishant</td>
                            <td class="text-center font-normal">Kedare</td>
                            <td class="text-center font-normal">10</td>
                            <td class="text-center font-normal">11 jan 2024</td>
                            <td class="text-center font-normal">Completed</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>