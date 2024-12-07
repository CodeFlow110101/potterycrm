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

<div class="h-full flex flex-col pb-8 pt-20">
    <div class="p-4">
        <div class="border h-0 w-full"></div>
    </div>
    <div class="grow flex flex-col gap-10">
        <div class="h-min grid grid-cols-1 gap-8">
            <div class="flex justify-between items-center gap-4 pl-3">
                <div class="flex justify-center items-center w-full">
                    <a href="/product" wire:navigate class="flex justify-center items-center gap-4 w-min p-2 rounded-md group hover:bg-amber-500 transition-colors duration-300">
                        <svg class="w-8 h-8 text-amber-500 group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M4.857 3A1.857 1.857 0 0 0 3 4.857v4.286C3 10.169 3.831 11 4.857 11h4.286A1.857 1.857 0 0 0 11 9.143V4.857A1.857 1.857 0 0 0 9.143 3H4.857Zm10 0A1.857 1.857 0 0 0 13 4.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 21 9.143V4.857A1.857 1.857 0 0 0 19.143 3h-4.286Zm-10 10A1.857 1.857 0 0 0 3 14.857v4.286C3 20.169 3.831 21 4.857 21h4.286A1.857 1.857 0 0 0 11 19.143v-4.286A1.857 1.857 0 0 0 9.143 13H4.857Zm10 0A1.857 1.857 0 0 0 13 14.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 21 19.143v-4.286A1.857 1.857 0 0 0 19.143 13h-4.286Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <div class="@if($path != 'product') invisible @endif border-2 rounded-full w-0 h-8 border-amber-500"></div>
            </div>
            <div class="flex justify-between items-center gap-4 pl-3">
                <div class="flex justify-center items-center w-full">
                    <a href="/booking" wire:navigate class="flex justify-center items-center gap-4 w-min p-2 rounded-md group hover:bg-amber-500 transition-colors duration-300">
                        <svg class="w-8 h-8 text-amber-500 group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6.01-6a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm-10 4a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
                <div class="@if($path != 'booking') invisible @endif border-2 rounded-full w-0 h-8 border-amber-500"></div>
            </div>
        </div>
        <div class="mt-auto flex flex-col gap-2">
            <div class="flex justify-between items-center pl-3">
                <div class="flex justify-center items-center w-full">
                    <a href="/setting" wire:navigate class="flex justify-start items-center gap-4 p-3 rounded-md group hover:bg-amber-500 transition-colors duration-300">
                        <div>
                            <svg class="w-8 h-8 text-amber-500 group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M9.586 2.586A2 2 0 0 1 11 2h2a2 2 0 0 1 2 2v.089l.473.196.063-.063a2.002 2.002 0 0 1 2.828 0l1.414 1.414a2 2 0 0 1 0 2.827l-.063.064.196.473H20a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-.089l-.196.473.063.063a2.002 2.002 0 0 1 0 2.828l-1.414 1.414a2 2 0 0 1-2.828 0l-.063-.063-.473.196V20a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2v-.089l-.473-.196-.063.063a2.002 2.002 0 0 1-2.828 0l-1.414-1.414a2 2 0 0 1 0-2.827l.063-.064L4.089 15H4a2 2 0 0 1-2-2v-2a2 2 0 0 1 2-2h.09l.195-.473-.063-.063a2 2 0 0 1 0-2.828l1.414-1.414a2 2 0 0 1 2.827 0l.064.063L9 4.089V4a2 2 0 0 1 .586-1.414ZM8 12a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </a>
                </div>
                <div class="border-2 rounded-full w-0 h-8 border-transparent"></div>
            </div>
            <div class="flex justify-between items-center pl-3">
                <div class="flex justify-center items-center w-full">
                    <button wire:click="signOut" class="flex justify-start items-center gap-4 p-3 rounded-md group hover:bg-amber-500 transition-colors duration-300">
                        <div>
                            <svg class="w-8 h-8 text-amber-500 group-hover:text-white transition-colors duration-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
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