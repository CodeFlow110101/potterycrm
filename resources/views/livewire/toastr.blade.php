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
    class="fixed cursor-pointer max-sm:inset-x-8 top-8 sm:right-8 z-50 backdrop-blur-2xl border-white border rounded-lg text-white">
    <div class="size-full p-4 bg-gradient-to-r rounded-xl flex justify-around items-center gap-6">
        <div class="rounded-full size-min">
            <div class="rounded-full p-2 border border-white">
                <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 11.917 9.724 16.5 19 7.5" />
                </svg>
            </div>
        </div>
        <div class="text-sm inter-700" x-text="message"></div>
    </div>
</div>