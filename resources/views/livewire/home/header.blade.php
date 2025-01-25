<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use function Livewire\Volt\{mount, state, computed};

state(['path', 'auth', 'routeName', 'booking_id']);
state(['cart'])->reactive();

$count = computed(function () {
    return collect($this->cart)->sum();
});

$signOut = function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    $this->redirectRoute('home', navigate: true);
};

mount(function ($cart, $path, $auth, $routeName) {
    $this->cart = $cart;
    $this->path = $path;
    $this->auth = $auth;
    $this->routeName = $routeName;
    $this->booking_id = request()->route('booking_id');
});
?>

<div>
    @if($path == 'checkout')
    <div class="w-3/4 py-4 mx-auto flex justify-between items-center">
        <a href="/" wire:navigate class="uppercase tracking-wider text-lg text-center text-primary font-avenir-next-rounded-semibold">
            Pottery Painting & Coffee
        </a>
        <a href="/cart{{ $booking_id ? '/' . $booking_id : '' }}" wire:navigate class="rounded-full hover:bg-black/10 p-2">
            <svg class="size-7 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10V6a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4m3-2 .917 11.923A1 1 0 0 1 17.92 21H6.08a1 1 0 0 1-.997-1.077L6 8h12Z" />
            </svg>
        </a>
    </div>
    @elseif($auth && in_array($routeName , ['booking','purchase','order','coupon','shop','product','cart','manage-coupon']))
    <div class="w-3/4 py-6 mx-auto flex flex-col gap-4">
        <div class="flex justify-between items-center">
            <a href="/" wire:navigate class="uppercase tracking-wider text-lg text-center text-primary font-avenir-next-rounded-semibold">
                <img class="h-12 w-auto" src="{{ asset('images/logo_no_name.jpeg') }}">
            </a>
            <div class="flex gap-4 items-center">
                <a href="/cart{{ $booking_id ? '/' . $booking_id : '' }}" wire:navigate class="rounded-full relative hover:bg-black/10 p-2">
                    <svg class="size-10 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 10V6a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4m3-2 .917 11.923A1 1 0 0 1 17.92 21H6.08a1 1 0 0 1-.997-1.077L6 8h12Z" />
                    </svg>
                    @if($this->count != 0)
                    <div class="absolute -top-2 -right-2 text-white bg-primary rounded-full text-xs size-6 aspect-square flex justify-center items-center">{{ $this->count }}</div>
                    @endif
                </a>
                <div class="relative group">
                    <svg class="size-10 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <div class="absolute hidden group-hover:block inset-x-0 -mx-16 pt-2 font-avenir-next-regular text-primary">
                        <div class="flex flex-col gap-2 mx-auto p-2 shadow border bg-white">
                            <button wire:click="signOut" class="flex items-center gap-2 outline-none">
                                <div>
                                    <svg class="size-5 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2" />
                                    </svg>
                                </div>
                                <div>Sign Out</div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="flex justify-start gap-12 uppercase font-avenir-next-regular">
            <a href="/shop" wire:navigate class="text-primary @if(request()->path() == 'shop') underline @else hover:underline @endif underline-offset-4">Shop</a>
            <a href="/booking" wire:navigate class="text-primary @if(request()->path() == 'booking') underline @else hover:underline @endif underline-offset-4">Booking</a>
            <a href="/purchase" wire:navigate class="text-primary @if(request()->path() == 'purchase') underline @else hover:underline @endif underline-offset-4">Purchase</a>
            <a href="/order" wire:navigate class="text-primary @if(request()->path() == 'order') underline @else hover:underline @endif underline-offset-4">Order</a>
            <a href="/coupon" wire:navigate class="text-primary @if(request()->path() == 'coupon') underline @else hover:underline @endif underline-offset-4">Coupon</a>
        </nav>
    </div>
    @else
    <div class="flex justify-center items-center w-11/12 mx-auto pt-12">
        <div class="flex-1"></div>
        <a href="/" wire:navigate class="flex flex-col gap-3">
            <img class="h-32 w-auto" src="{{ asset('images/logo_no_name.jpeg') }}">
            <div class="w-full border border-primary rounded-full"></div>
            <div class="uppercase tracking-widest text-xl text-center text-primary font-avenir-next-rounded-semibold">Pottery Painting <br> & Coffee</div>
        </a>
        <div class="flex-1 flex justify-end gap-4">
            <a href="/cart{{ $booking_id ? '/' . $booking_id : '' }}" wire:navigate class="relative rounded-full hover:bg-black/10 p-2">
                <svg class="size-7 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10V6a3 3 0 0 1 3-3v0a3 3 0 0 1 3 3v4m3-2 .917 11.923A1 1 0 0 1 17.92 21H6.08a1 1 0 0 1-.997-1.077L6 8h12Z" />
                </svg>
                @if($this->count != 0)
                <div class="absolute -top-4 -right-4 text-white bg-primary rounded-full text-xs size-6 aspect-square flex justify-center items-center">{{ $this->count }}</div>
                @endif
            </a>
        </div>
    </div>
    <nav class="flex justify-center gap-12 my-10 text-lg uppercase font-avenir-next-regular">
        <a href="/sign-in" wire:navigate class="text-primary @if(request()->path() == 'sign-in') underline @else hover:underline @endif underline-offset-4">Sign In</a>
        <a href="/register" wire:navigate class="text-primary @if(request()->path() == 'register') underline @else hover:underline @endif underline-offset-4">Register</a>
        <a href="/book-table" wire:navigate class="text-primary @if(request()->path() == 'book-table') underline @else hover:underline @endif underline-offset-4">Book a Table</a>
        <a href="/shop" wire:navigate class="text-primary @if(request()->path() == 'shop') underline @else hover:underline @endif underline-offset-4">Shop</a>
        <a href="/about-us" wire:navigate class="text-primary @if(request()->path() == 'about-us') underline @else hover:underline @endif underline-offset-4">About Us</a>
        <a href="/contact-us" wire:navigate class="text-primary @if(request()->path() == 'contact-us') underline @else hover:underline @endif underline-offset-4">Contact</a>
        <a href="/faq" wire:navigate class="text-primary @if(request()->path() == 'faq') underline @else hover:underline @endif underline-offset-4">FAQ</a>
    </nav>
    @endif
    <div class="border"></div>
</div>