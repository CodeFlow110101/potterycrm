<?php

use function Livewire\Volt\{state};

//

?>

<div x-cloak x-data="toastr" x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90"
    x-on:show-toastr.window="toggle($event.detail)"
    :class="type == 'success' ? 'shadow-green-700' : 'shadow-red-700'"
    class="fixed cursor-pointer top-8 right-8 z-50 bg-white shadow-lg rounded-xl">
    <div :class="type == 'success' ? 'from-green-700/40' : 'from-red-700/40'" class="size-full py-4 pl-6 pr-24 bg-gradient-to-r bg-white/10 rounded-xl flex justify-around items-center gap-6">
        <div :class="type=='success' ? ' bg-green-700/20' : 'bg-red-700/20'" class="rounded-full p-3 size-min">
            <div :class=" type=='success' ? ' bg-green-700' : 'bg-red-700'" class=" rounded-full p-2">
                <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 11.917 9.724 16.5 19 7.5" />
                </svg>
            </div>
        </div>
        <div class="h-min grid grid-cols-1 gap-2 text-black">
            <div class="text-xl inter-700 capitalize" x-text="type"></div>
            <div class="text-sm inter-700" x-text="message"></div>
        </div>
    </div>
    <div class="from-red-700 from-green-700 from-indigo-700 from-amber-700 hidden"></div>
</div>