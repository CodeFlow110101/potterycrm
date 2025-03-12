<?php

use function Livewire\Volt\{state};

//

?>

<div class="grow flex flex-col overflow-y-auto hidden-scrollbar text-white">
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="absolute inset-x-0 flex max-lg:flex-col gap-4 grow" :style="'height: ' + height + 'px;'">
            <div class="grow flex max-lg:flex-col relative max-lg:gap-4">
                <div class="absolute inset-0 pointer-events-none flex flex-col max-lg:hidden">
                    <div class="overflow-hidden size-full">
                        <img class="translate-y-1/2 size-full" src="{{ asset('images/home_hand.png') }}">
                    </div>
                </div>
                <div class="grow flex flex-col lg:py-16">
                    <div class="flex justify-center grow">
                        <div class="flex flex-col text-7xl sm:text-8xl lg:text-9xl xl:w-3/4 w-11/12 mx-auto uppercase font-avenir-next-regular tracking-tighter">
                            Pottery<br><span class="font-avenir-next-bold">Painting</span>& Coffee
                        </div>
                    </div>
                    <div class="flex flex-col w-11/12 mx-auto gap-2">
                        <div class="mt-auto flex justify-start gap-2 pr-4">
                            <div class="bg-white rounded-full p-1">
                                <svg class="size-10 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path fill="currentColor" fill-rule="evenodd" d="M3 8a5 5 0 0 1 5-5h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8Zm5-3a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H8Zm7.597 2.214a1 1 0 0 1 1-1h.01a1 1 0 1 1 0 2h-.01a1 1 0 0 1-1-1ZM12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm-5 3a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="bg-white rounded-full p-1">
                                <svg class="size-10 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M13.135 6H15V3h-1.865a4.147 4.147 0 0 0-4.142 4.142V9H7v3h2v9.938h3V12h2.021l.592-3H12V6.591A.6.6 0 0 1 12.592 6h.543Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex">
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7.978 4a2.553 2.553 0 0 0-1.926.877C4.233 6.7 3.699 8.751 4.153 10.814c.44 1.995 1.778 3.893 3.456 5.572 1.68 1.679 3.577 3.018 5.57 3.459 2.062.456 4.115-.073 5.94-1.885a2.556 2.556 0 0 0 .001-3.861l-1.21-1.21a2.689 2.689 0 0 0-3.802 0l-.617.618a.806.806 0 0 1-1.14 0l-1.854-1.855a.807.807 0 0 1 0-1.14l.618-.62a2.692 2.692 0 0 0 0-3.803l-1.21-1.211A2.555 2.555 0 0 0 7.978 4Z" />
                            </svg>
                            <div>{{ env('TWILIO_PHONE_COUNTRY_CODE') . ' ' . env('ADMIN_PHONE_NO') }}</div>
                        </div>
                        <div class="mt-auto text-sm lg:text-2xl font-avenir-next-rounded-light tracking-wider uppercase">Welcome to<br>our website</div>
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="overflow-x-hidden hidden-scrollbar pointer-events-none">
                        <div class="aspect-square lg:rounded-full  max-lg:w-11/12 max-lg:mx-auto rounded-2xl p-4 lg:translate-x-16 lg:pl-8 lg:pr-20 flex justify-center items-center bg-indigo-800/90 text-right leading-tight">
                            <div class="flex flex-col gap-4 max-xl:text-xs">
                                <div class="text-center text-lg font-avenir-next-rounded-extra-bold">Walk-ins Welcome!</div>
                                <div>
                                    Welcome to the perfect place for creativity and fun!
                                </div>
                                <div>
                                    Our pottery painting cafe is a place where everyone
                                    <br class="max-xl:hidden">
                                    can relax, unwind, and get artistic. Choose from a wide
                                    <br class="max-xl:hidden">
                                    selection of pottery pieces, grab your favorite
                                    <br class="max-xl:hidden">
                                    colours, and let your imagination run wild.
                                </div>
                                <div>
                                    No experience necessary – we provide all the supplies
                                    <br class="max-xl:hidden">
                                    and guidance you need to create a masterpiece. Great
                                    <br class="max-xl:hidden">
                                    for families, friends, parties, Date Nights or just a solo
                                    <br class="max-xl:hidden">
                                    art therapy session.
                                </div>
                                <div>
                                    Come in, paint, and make memories that last!
                                </div>
                                <div>
                                    P.S we make great coffee too
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <div class="border-t pt-4 pr-4">
                            <a href="book-table" wire:navigate class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight">Book Now</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-11/12 mx-auto gap-6 lg:gap-8 py-4 lg:py-8 text-white grow flex flex-col lg:hidden">
                <livewire:section.header header="How does it work?" />
                <livewire:section.how-it-works />

                <livewire:section.header header="Private Groups" />
                <livewire:section.private-groups />

                <!-- <livewire:section.header header="Classes" />
                <livewire:section.classes /> -->
            </div>
        </div>
    </div>
</div>