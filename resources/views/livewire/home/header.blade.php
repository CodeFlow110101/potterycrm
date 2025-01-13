<?php

use function Livewire\Volt\{state};

?>

<div class="pt-12">
    <div class="flex justify-center items-center w-11/12 mx-auto">
        <div class="flex-1"></div>
        <a href="/" wire:navigate class="flex flex-col gap-3">
            <img class="h-36 w-auto" src="{{ asset('images/logo_no_name.jpeg') }}">
            <div class="w-full border border-primary rounded-full"></div>
            <div class="uppercase tracking-widest text-xl text-center text-primary font-avenir-next-rounded-semibold">Pottery Painting <br> & Coffee</div>
        </a>
        <div class="flex-1 flex justify-end gap-4">
            <div>
                <svg class="size-7 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <div>
                <svg class="size-7 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10V6a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4m3-2 .917 11.923A1 1 0 0 1 17.92 21H6.08a1 1 0 0 1-.997-1.077L6 8h12Z" />
                </svg>
            </div>
        </div>
    </div>
    <nav class="flex justify-center gap-12 my-10 text-lg uppercase font-avenir-next-regular">
        <a href="/sign-in" wire:navigate class="text-primary @if(request()->path() == 'sign-in') underline @else hover:underline @endif underline-offset-4">Sign In</a>
        <a href="/register" wire:navigate class="text-primary @if(request()->path() == 'register') underline @else hover:underline @endif underline-offset-4">Register</a>
        <a href="/book-table" wire:navigate class="text-primary @if(request()->path() == 'book-table') underline @else hover:underline @endif underline-offset-4">Book a Table</a>
    </nav>
    <div class="border"></div>
</div>