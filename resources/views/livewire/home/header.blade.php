<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use function Livewire\Volt\{mount, state, computed};

state(['path', 'auth', 'routeName', 'booking_id']);
state(['cart'])->reactive();

$count = computed(function () {
    return collect($this->cart)->sum();
});

$logOut = function (Request $request) {
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
    <nav class="flex justify-between items-center mt-5 mx-auto uppercase font-avenir-next-bold text-white">
        <a href="/" wire:navigate class="text-3xl mx-14 tracking-widest">ICONA</a>
        <div x-data class="flex justify-evenly items-center w-full *:underline-offset-4">
            @if(!$auth)
            <a href="how-it-works" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">How it Works</a>
            <a href="private-groups" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Private Groups</a>
            <a href="classes" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Classes</a>
            @endif
            <a href="shop" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Shop</a>
            @if($auth)
            <a href="booking" wire:current="font-bold text-white" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Booking</a>
            @if(Gate::check('admin'))
            <a href="/register" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Register</a>
            @endif
            <a href="purchase" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Purchase</a>
            <a href="order" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Order</a>
            <a href="coupon" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Coupon</a>
            @endif
            @if(!$auth)
            <a href="book-table" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Book a Table</a>
            <a href="log-in" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Log In</a>
            <a href="about-us" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">About Us</a>
            <a href="contact-us" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Contact</a>
            <a href="faq" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">FAQ</a>
            @endif
            @if($auth)
            <div class="flex justify-between items-center relative group">
                <div class="relative group">
                    <svg class="size-7 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M12 20a7.966 7.966 0 0 1-5.002-1.756l.002.001v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z" clip-rule="evenodd" />
                    </svg>
                    <div class="absolute hidden group-hover:block inset-x-0 z-20 -mx-16 pt-2 font-avenir-next-regular text-black">
                        <button wire:click="logOut" class="flex items-center mx-auto gap-2 outline-none rounded-md bg-white py-2 px-6 shadow border">Log Out </button>
                    </div>
                </div>
            </div>
            @endif
            <a href="/cart{{ $booking_id ? '/' . $booking_id : '' }}" wire:navigate class="relative rounded-full hover:bg-black/10">
                <svg class="size-7 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M4 4a1 1 0 0 1 1-1h1.5a1 1 0 0 1 .979.796L7.939 6H19a1 1 0 0 1 .979 1.204l-1.25 6a1 1 0 0 1-.979.796H9.605l.208 1H17a3 3 0 1 1-2.83 2h-2.34a3 3 0 1 1-4.009-1.76L5.686 5H5a1 1 0 0 1-1-1Z" clip-rule="evenodd" />
                </svg>
                @if($this->count != 0)
                <div class="absolute -top-4 -right-4 text-black bg-white rounded-full text-xs size-6 aspect-square flex justify-center items-center">{{ $this->count }}</div>
                @endif
            </a>
        </div>
    </nav>
</div>