<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use function Livewire\Volt\{state, mount};

state(['path']);

$signOut = function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    $this->redirectRoute('home', navigate: true);
};

mount(function ($path) {
    $this->path = $path;
});
?>

<div class="h-full flex flex-col gap-6 pb-8 pt-8 px-2">
    <div class="w-full">
        <img class="w-full h-12" src="{{ asset('images/logo_no_name.jpeg') }}">
    </div>
    <div>
        <div class="border h-0 w-full"></div>
    </div>
    <div class="grow flex flex-col gap-10">
        <div class="h-min grid grid-cols-1 gap-6">
            <div class="flex justify-between items-center gap-4 pl-3">
                <div class="flex justify-center items-center w-full">
                    <a href="/product" wire:navigate class="flex justify-center items-center gap-4 w-min p-1 rounded-md group hover:bg-primary transition-colors duration-300">
                        <svg class="w-8 h-8 text-primary group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M4.857 3A1.857 1.857 0 0 0 3 4.857v4.286C3 10.169 3.831 11 4.857 11h4.286A1.857 1.857 0 0 0 11 9.143V4.857A1.857 1.857 0 0 0 9.143 3H4.857Zm10 0A1.857 1.857 0 0 0 13 4.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 21 9.143V4.857A1.857 1.857 0 0 0 19.143 3h-4.286Zm-10 10A1.857 1.857 0 0 0 3 14.857v4.286C3 20.169 3.831 21 4.857 21h4.286A1.857 1.857 0 0 0 11 19.143v-4.286A1.857 1.857 0 0 0 9.143 13H4.857Zm10 0A1.857 1.857 0 0 0 13 14.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 21 19.143v-4.286A1.857 1.857 0 0 0 19.143 13h-4.286Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <div class="@if($path != 'product') invisible @endif border-2 rounded-full w-0 h-8 border-primary"></div>
            </div>
            <div class="flex justify-between items-center gap-4 pl-3">
                <div class="flex justify-center items-center w-full">
                    <a href="/booking" wire:navigate class="flex justify-center items-center gap-4 w-min p-1 rounded-md group hover:bg-primary transition-colors duration-300">
                        <svg class="w-8 h-8 text-primary group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6.01-6a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm-10 4a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <div class="@if($path != 'booking') invisible @endif border-2 rounded-full w-0 h-8 border-primary"></div>
            </div>
            <div class="flex justify-between items-center gap-4 pl-3">
                <div class="flex justify-center items-center w-full">
                    <a href="/purchase" wire:navigate class="flex justify-center items-center gap-4 w-min p-1 rounded-md group hover:bg-primary transition-colors duration-300">
                        <svg class="w-8 h-8 text-primary group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M7 6a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-2v-4a3 3 0 0 0-3-3H7V6Z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M2 11a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-7Zm7.5 1a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z" clip-rule="evenodd" />
                            <path d="M10.5 14.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" />
                        </svg>
                    </a>
                </div>
                <div class="@if($path != 'purchase') invisible @endif border-2 rounded-full w-0 h-8 border-primary"></div>
            </div>
            <div class="flex justify-between items-center gap-4 pl-3">
                <div class="flex justify-center items-center w-full">
                    <a href="/order" wire:navigate class="flex justify-center items-center gap-4 w-min p-1 rounded-md group hover:bg-primary transition-colors duration-300">
                        <svg class="w-8 h-8 text-primary group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 0 0-2 2v9a1 1 0 0 0 1 1h.535a3.5 3.5 0 1 0 6.93 0h3.07a3.5 3.5 0 1 0 6.93 0H21a1 1 0 0 0 1-1v-4a.999.999 0 0 0-.106-.447l-2-4A1 1 0 0 0 19 6h-5a2 2 0 0 0-2-2H4Zm14.192 11.59.016.02a1.5 1.5 0 1 1-.016-.021Zm-10 0 .016.02a1.5 1.5 0 1 1-.016-.021Zm5.806-5.572v-2.02h4.396l1 2.02h-5.396Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <div class="@if($path != 'order') invisible @endif border-2 rounded-full w-0 h-8 border-primary"></div>
            </div>
            <div class="flex justify-between items-center gap-4 pl-3">
                <div class="flex justify-center items-center w-full">
                    <a href="/coupon" wire:navigate class="flex justify-center items-center gap-4 w-min p-1 rounded-md group hover:bg-primary transition-colors duration-300">
                        <svg class="w-8 h-8 text-primary group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M20.29 8.567c.133.323.334.613.59.85v.002a3.536 3.536 0 0 1 0 5.166 2.442 2.442 0 0 0-.776 1.868 3.534 3.534 0 0 1-3.651 3.653 2.483 2.483 0 0 0-1.87.776 3.537 3.537 0 0 1-5.164 0 2.44 2.44 0 0 0-1.87-.776 3.533 3.533 0 0 1-3.653-3.654 2.44 2.44 0 0 0-.775-1.868 3.537 3.537 0 0 1 0-5.166 2.44 2.44 0 0 0 .775-1.87 3.55 3.55 0 0 1 1.033-2.62 3.594 3.594 0 0 1 2.62-1.032 2.401 2.401 0 0 0 1.87-.775 3.535 3.535 0 0 1 5.165 0 2.444 2.444 0 0 0 1.869.775 3.532 3.532 0 0 1 3.652 3.652c-.012.35.051.697.184 1.02ZM9.927 7.371a1 1 0 1 0 0 2h.01a1 1 0 0 0 0-2h-.01Zm5.889 2.226a1 1 0 0 0-1.414-1.415L8.184 14.4a1 1 0 0 0 1.414 1.414l6.218-6.217Zm-2.79 5.028a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <div class="@if($path != 'coupon') invisible @endif border-2 rounded-full w-0 h-8 border-primary"></div>
            </div>
        </div>
        <div class="mt-auto flex flex-col gap-2">
            <div class="flex justify-between items-center pl-3">
                <div class="flex justify-center items-center w-full">
                    <a href="/setting" wire:navigate class="flex justify-start items-center gap-4 p-3 rounded-md group hover:bg-primary transition-colors duration-300">
                        <div>
                            <svg class="w-8 h-8 text-primary group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M9.586 2.586A2 2 0 0 1 11 2h2a2 2 0 0 1 2 2v.089l.473.196.063-.063a2.002 2.002 0 0 1 2.828 0l1.414 1.414a2 2 0 0 1 0 2.827l-.063.064.196.473H20a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-.089l-.196.473.063.063a2.002 2.002 0 0 1 0 2.828l-1.414 1.414a2 2 0 0 1-2.828 0l-.063-.063-.473.196V20a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-.089l-.473-.196-.063.063a2.002 2.002 0 0 1-2.828 0l-1.414-1.414a2 2 0 0 1 0-2.827l.063-.064L4.089 15H4a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2h.09l.195-.473-.063-.063a2 2 0 0 1 0-2.828l1.414-1.414a2 2 0 0 1 2.827 0l.064.063L9 4.089V4a2 2 0 0 1 .586-1.414ZM8 12a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </a>
                </div>
                <div class="border-2 rounded-full w-0 h-8 border-transparent"></div>
            </div>
            <div class="flex justify-between items-center pl-3">
                <div class="flex justify-center items-center w-full">
                    <button wire:click="signOut" class="flex justify-start items-center gap-4 p-3 rounded-md group hover:bg-primary transition-colors duration-300">
                        <div>
                            <svg class="w-8 h-8 text-primary group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
                            </svg>
                        </div>
                    </button>
                </div>
                <div class="border-2 rounded-full w-0 h-8 border-transparent"></div>
            </div>
        </div>
    </div>
</div>