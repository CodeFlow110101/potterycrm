<?php

use function Livewire\Volt\{state, mount};

mount(fn() => $this->dispatch('clear-cart'))

?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="grow flex backdrop-blur-xl border boreder-white rounded-lg text-white">
        <div class="m-auto flex flex-col gap-4">
            <div class="rounded-full size-min mx-auto flex justify-center items-center p-4 border border-white">
                <svg class="size-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                </svg>
            </div>
            <div class="border border-white p-4 text-xl rounded-lg flex flex-col gap-4">
                <div class="text-center">Order Placed Successfully</div>
                <div class="flex justify-center items-center">
                    <a href="/shop" class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight text-base flex items-center gap-4" wire:navigate>
                        <div>Shop</div>
                        <div>
                            <svg class="w-6 h-6 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                            </svg>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>