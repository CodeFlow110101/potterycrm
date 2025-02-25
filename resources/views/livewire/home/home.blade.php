<?php

use function Livewire\Volt\{state};

//

?>

<div class="grow flex flex-col overflow-y-auto hidden-scrollbar text-white">
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="absolute inset-x-0 flex max-lg:flex-col grow">
            <div class="grow flex max-lg:flex-col relative">
                <div class="absolute inset-0 pointer-events-none flex flex-col max-lg:hidden">
                    <div class="overflow-hidden size-full">
                        <img class="translate-y-2/4 size-full" src="{{ asset('images/home_hand.png') }}">
                    </div>
                </div>
                <div class="grow flex flex-col lg:py-16">
                    <div class="flex justify-center grow">
                        <div class="flex flex-col text-7xl sm:text-8xl lg:text-9xl lg:w-3/4 w-11/12 mx-auto uppercase font-avenir-next-regular tracking-tighter">
                            Pottery<br><span class="font-avenir-next-bold">Painting</span>& Coffee
                        </div>
                    </div>
                    <div class="mt-auto w-11/12 mx-auto text-sm lg:text-2xl font-avenir-next-rounded-light tracking-wider uppercase">Welcome to<br>our website</div>
                </div>
                <div class="flex flex-col gap-4 sm:gap-12">
                    <div class="overflow-x-hidden">
                        <div class="aspect-square sm:rounded-full mt-10 ml-auto max-sm:w-11/12 max-sm:mx-auto rounded-2xl p-4 sm:translate-x-16 sm:pl-8 sm:pr-20 flex justify-center items-center bg-indigo-800/90 text-right leading-tight">
                            <div class="flex flex-col gap-4">
                                <div>
                                    Welcome to the perfect place for creativity and fun!
                                </div>
                                <div>
                                    Our pottery painting cafe is a place where everyone
                                    <br class="max-sm:hidden">
                                    can relax, unwind, and get artistic. Choose from a wide
                                    <br class="max-sm:hidden">
                                    selection of pottery pieces, grab your favorite
                                    <br class="max-sm:hidden">
                                    colours, and let your imagination run wild.
                                </div>
                                <div>
                                    No experience necessary – we provide all the supplies
                                    <br class="max-sm:hidden">
                                    and guidance you need to create a masterpiece. Great
                                    <br class="max-sm:hidden">
                                    for families, friends, parties, Date Nights or just a solo
                                    <br class="max-sm:hidden">
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
                            <button class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight">Subscribe</button>
                        </div>
                    </div>
                    <div class="mt-auto flex justify-end gap-2 pr-4">
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
                </div>
            </div>
        </div>
    </div>
</div>