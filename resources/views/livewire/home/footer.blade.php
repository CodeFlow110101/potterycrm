<?php

use function Livewire\Volt\{state, mount};

state(['routeName', 'auth']);

mount(function ($routeName, $auth) {
    $this->routeName = $routeName;
});

?>

<div class="pb-6 text-primary mt-auto hidden">
    @if(!($auth && in_array($routeName , ['booking','purchase','order','coupon','shop','product','cart','manage-coupon'])))
    <div class="my-20 flex justify-evenly gap-10 w-3/4 mx-auto">
        <div class="flex flex-col gap-8">
            <div class="font-avenir-next-rounded-light">Useful Links</div>
            <div class="font-avenir-next-rounded-regular flex flex-col gap-1">
                <div class="hover:underline underline-offset-4">Employment</div>
                <div class="hover:underline underline-offset-4">Student Resources</div>
                <div class="hover:underline underline-offset-4">Guest Passes (for Members)</div>
                <div class="hover:underline underline-offset-4">Class Policies</div>
                <div class="hover:underline underline-offset-4">Shop Policies + Shipping</div>
                <div class="hover:underline underline-offset-4">Do not sell my personal <br> information</div>
            </div>
        </div>
        <div class="flex flex-col gap-8">
            <div class="font-avenir-next-rounded-light">Contact Us</div>
            <div class="font-avenir-next-rounded-regular">
                For any questions about classes, <br>
                membership, or the studio, <br>
                <span class="underline">contact us here.</span>
            </div>
        </div>
        <div class="flex-1 flex flex-col gap-4">
            <div class="font-avenir-next-rounded-light">Newsletter</div>
            <div class="flex">
                <input class="w-full flex-1 border rounded-l outline-none py-2 px-4 font-avenir-next-rounded-light placeholder:text-primary placeholder:text-opacity-50" placeholder="Email Address">
                <div>
                    <button class="bg-primary bg-opacity-90 hover:bg-opacity-100 text-white py-3 px-6 uppercase font-avenir-next-rounded-extra-light tracking-wider">Subscribe</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="border mt-auto"></div>
    <div class="flex flex-col gap-4 mx-auto w-3/4 py-4">
        <div>
            <svg class="size-8 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path fill="currentColor" fill-rule="evenodd" d="M3 8a5 5 0 0 1 5-5h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8Zm5-3a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H8Zm7.597 2.214a1 1 0 0 1 1-1h.01a1 1 0 1 1 0 2h-.01a1 1 0 0 1-1-1ZM12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm-5 3a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z" clip-rule="evenodd" />
            </svg>
        </div>
        <a href="/" wire:navigate class="font-avenir-next-rounded-light text-sm group">
            © {{ now()->year }} <span class="group-hover:underline">Icona Pottery Painting & Cafe</span>
        </a>
    </div>
</div>