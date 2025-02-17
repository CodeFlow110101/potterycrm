<?php

use function Livewire\Volt\{state};

//

?>

<div class="grow flex flex-col overflow-y-auto hidden-scrollbar text-white">
    <div class="grow flex relative">
        <div class="absolute inset-0 pointer-events-none flex flex-col">
            <div class="overflow-hidden size-full">
                <img class="translate-y-2/4 size-full" src="{{ asset('images/home_hand.png') }}">
            </div>
        </div>
        <div class="grow flex flex-col py-16">
            <div class="flex justify-center grow">
                <div class="flex flex-col text-9xl w-3/4 mx-auto uppercase font-avenir-next-regular tracking-tighter">
                    Pottery<br><span class="font-avenir-next-bold">Painting</span>& Coffee
                </div>
            </div>
            <div class="mt-auto w-11/12 mx-auto text-2xl font-avenir-next-rounded-light tracking-wider uppercase">Welcome to<br>our website</div>
        </div>
        <div class="flex flex-col gap-4">
            <div class="overflow-x-hidden">
                <div class="aspect-square rounded-full mt-10 translate-x-16 pl-8 pr-20 flex justify-center items-center bg-indigo-800/90 text-right leading-tight">
                    <div class="flex flex-col gap-4">
                        <div>
                            Welcome to the perfect place for creativity and fun!
                        </div>
                        <div>
                            our pottery painting cafe is a place where everyone
                            <br>
                            can relax, unwind, and get artistic. Choose from a wide
                            <br>
                            selection of pottery pieces, grab your favorite
                            <br>
                            colours, and let your imagination run wild.
                        </div>
                        <div>
                            No experience necessary – we provide all the supplies
                            <br>
                            and guidance you need to create a masterpiece. Great
                            <br>
                            for families, friends, parties, Date Nights or just a solo
                            <br>
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