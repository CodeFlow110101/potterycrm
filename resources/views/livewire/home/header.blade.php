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

<div x-data="{ show: false }" :class="show && 'max-lg:backdrop-blur-xl max-lg:fixed max-lg:inset-0 max-lg:z-50 relative bg-black/40'" x-resize="height = $height">
    <nav :class="show && 'w-11/12 mx-auto'" class="flex justify-between items-center max-lg:items-start max-lg:w-11/12 pt-5 mx-auto uppercase font-avenir-next-bold text-white">
        <div class="flex-1 flex max-lg:flex-col gap-5">
            <a :class="show && 'text-center'" href="/" wire:navigate class="text-3xl lg:mx-14 tracking-widest">ICONA</a>
            <div :class="show ? 'flex-col gap-4' : 'max-lg:hidden'" class="flex justify-evenly items-center w-full *:underline-offset-4">
                @if(!$auth)
                <a href="how-it-works" wire:navigate class="max-sm:hidden" :class="$wire.routeName === $el.getAttribute('href') && 'underline'">How it Works</a>
                <a href="private-groups" wire:navigate class="max-sm:hidden" :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Private Groups</a>
                <a href="classes" wire:navigate class="max-sm:hidden" :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Classes</a>
                @endif
                <a href="shop" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Shop</a>
                @if($auth)
                <a href="booking" wire:current="font-bold text-white" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Booking</a>
                @can('register-user')
                <a href="register" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Register</a>
                @endcan
                <a href="purchase" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Purchase</a>
                <a href="order" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Order</a>
                <a href="coupon" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">Coupon</a>
                @can('update-user')
                <a href="user" wire:navigate :class="$wire.routeName === $el.getAttribute('href') && 'underline'">User</a>
                @endcan
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
                    <div class="sm:relative sm:group">
                        <svg class="size-7 text-white max-sm:hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 20a7.966 7.966 0 0 1-5.002-1.756l.002.001v-.683c0-1.794 1.492-3.25 3.333-3.25h3.334c1.84 0 3.333 1.456 3.333 3.25v.683A7.966 7.966 0 0 1 12 20ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10c0 5.5-4.44 9.963-9.932 10h-.138C6.438 21.962 2 17.5 2 12Zm10-5c-1.84 0-3.333 1.455-3.333 3.25S10.159 13.5 12 13.5c1.84 0 3.333-1.455 3.333-3.25S13.841 7 12 7Z" clip-rule="evenodd" />
                        </svg>
                        <div class="sm:absolute sm:hidden sm:group-hover:block sm:inset-x-0 sm:z-20 sm:-mx-16 sm:pt-2 font-avenir-next-regular text-black flex flex-col gap-12">
                            <button wire:click="logOut" class="flex items-center mx-auto gap-2 outline-none rounded-md bg-white py-2 px-6 shadow border">Log Out </button>
                        </div>
                    </div>
                </div>
                @endif
                <a href="/cart" wire:navigate class="relative rounded-full hover:bg-black/10">
                    <svg class="size-7 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M4 4a1 1 0 0 1 1-1h1.5a1 1 0 0 1 .979.796L7.939 6H19a1 1 0 0 1 .979 1.204l-1.25 6a1 1 0 0 1-.979.796H9.605l.208 1H17a3 3 0 1 1-2.83 2h-2.34a3 3 0 1 1-4.009-1.76L5.686 5H5a1 1 0 0 1-1-1Z" clip-rule="evenodd" />
                    </svg>
                    @if($this->count != 0)
                    <div class="absolute -top-4 -right-4 text-black bg-white rounded-full text-xs size-6 aspect-square flex justify-center items-center">{{ $this->count }}</div>
                    @endif
                </a>
            </div>
            <div @click="show = !show" class="lg:hidden mx-auto">
                <svg x-show="show" class="w-8 h-8 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                </svg>
            </div>
        </div>
        <div @click="show = !show" class="lg:hidden">
            <svg x-show="!show" class="w-8 h-8 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
            </svg>
        </div>
    </nav>
</div>