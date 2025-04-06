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
                        <div class="mt-auto flex justify-start gap-2 pr-4 *:bg-white *:rounded-full *:p-1">
                            <a href="{{ env('INSTAGRAM_URL') }}" target="_blank">
                                <svg class="size-8 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path fill="currentColor" fill-rule="evenodd" d="M3 8a5 5 0 0 1 5-5h8a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5V8Zm5-3a3 3 0 0 0-3 3v8a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H8Zm7.597 2.214a1 1 0 0 1 1-1h.01a1 1 0 1 1 0 2h-.01a1 1 0 0 1-1-1ZM12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Zm-5 3a5 5 0 1 1 10 0 5 5 0 0 1-10 0Z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="{{ env('FACEBOOK_URL') }}" target="_blank">
                                <svg class="size-8 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M13.135 6H15V3h-1.865a4.147 4.147 0 0 0-4.142 4.142V9H7v3h2v9.938h3V12h2.021l.592-3H12V6.591A.6.6 0 0 1 12.592 6h.543Z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="{{ env('TIKTOK_URL') }}" target="_blank">
                                <svg class="size-8" fill="#000000" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">
                                    <path d="M19.589 6.686a4.793 4.793 0 0 1-3.77-4.245V2h-3.445v13.672a2.896 2.896 0 0 1-5.201 1.743l-.002-.001.002.001a2.895 2.895 0 0 1 3.183-4.51v-3.5a6.329 6.329 0 0 0-5.394 10.692 6.33 6.33 0 0 0 10.857-4.424V8.687a8.182 8.182 0 0 0 4.773 1.526V6.79a4.831 4.831 0 0 1-1.003-.104z" />
                                </svg>
                            </a>
                            <a href="{{ env('YOUTUBE_URL') }}" target="_blank">
                                <svg class="size-8 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M21.7 8.037a4.26 4.26 0 0 0-.789-1.964 2.84 2.84 0 0 0-1.984-.839c-2.767-.2-6.926-.2-6.926-.2s-4.157 0-6.928.2a2.836 2.836 0 0 0-1.983.839 4.225 4.225 0 0 0-.79 1.965 30.146 30.146 0 0 0-.2 3.206v1.5a30.12 30.12 0 0 0 .2 3.206c.094.712.364 1.39.784 1.972.604.536 1.38.837 2.187.848 1.583.151 6.731.2 6.731.2s4.161 0 6.928-.2a2.844 2.844 0 0 0 1.985-.84 4.27 4.27 0 0 0 .787-1.965 30.12 30.12 0 0 0 .2-3.206v-1.516a30.672 30.672 0 0 0-.202-3.206Zm-11.692 6.554v-5.62l5.4 2.819-5.4 2.801Z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                        <div class="flex">
                            <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7.978 4a2.553 2.553 0 0 0-1.926.877C4.233 6.7 3.699 8.751 4.153 10.814c.44 1.995 1.778 3.893 3.456 5.572 1.68 1.679 3.577 3.018 5.57 3.459 2.062.456 4.115-.073 5.94-1.885a2.556 2.556 0 0 0 .001-3.861l-1.21-1.21a2.689 2.689 0 0 0-3.802 0l-.617.618a.806.806 0 0 1-1.14 0l-1.854-1.855a.807.807 0 0 1 0-1.14l.618-.62a2.692 2.692 0 0 0 0-3.803l-1.21-1.211A2.555 2.555 0 0 0 7.978 4Z" />
                            </svg>
                            <div>{{ env('TWILIO_PHONE_COUNTRY_CODE') . ' ' . env('ADMIN_PHONE_NO') }}</div>
                        </div>
                        <div class="mt-auto text-sm lg:text-2xl font-avenir-next-rounded-light tracking-wider uppercase">Welcome to<br>our website</div>
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="overflow-x-hidden hidden-scrollbar pointer-events-none grow flex flex-col justify-center">
                        <div class="flex justify-end max-lg:w-11/12 max-lg:mx-auto text-right leading-tight">
                            <div class="flex text-center flex-col gap-4 max-xl:text-xs lg:whitespace-nowrap lg:w-min relative z-10 max-lg:bg-indigo-800/90 max-lg:rounded-xl py-8 max-lg:px-4">
                                <div class="max-lg:hidden absolute -inset-10 bg-indigo-800/90 -z-10 rounded-full aspect-square"></div>
                                <div class="text-lg font-avenir-next-rounded-extra-bold">Walk-ins Welcome!</div>
                                <div>
                                    Welcome to the perfect place for creativity and fun!
                                </div>
                                <div>
                                    Our pottery painting cafe is a place where everyone
                                    <br class="max-lg:hidden">
                                    can relax, unwind, and get artistic. Choose from a wide
                                    <br class="max-lg:hidden">
                                    selection of pottery pieces, grab your favorite
                                    <br class="max-lg:hidden">
                                    colours, and let your imagination run wild.
                                </div>
                                <div>
                                    No experience necessary – we provide all the supplies
                                    <br class="max-lg:hidden">
                                    and guidance you need to create a masterpiece. Great
                                    <br class="max-lg:hidden">
                                    for families, friends, parties, Date Nights or just a solo
                                    <br class="max-lg:hidden">
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
                        <div class="border-t pt-4 pr-4 flex flex-col gap-1">
                            <a href="book-table" wire:navigate class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight">Book Now</a>
                        </div>
                    </div>
                    <div class="text-xs flex justify-end w-1/2 ml-auto">
                        <div class="text-end">
                            &copy; 2025 <span class="font-semibold">Icona Pottery Cafe</span>. All rights reserved. No part of this website may be reproduced or transmitted in any form without prior written permission.
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