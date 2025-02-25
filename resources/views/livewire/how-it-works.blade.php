<?php

use function Livewire\Volt\{state};

//

?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="text-5xl lg:text-7xl font-avenir-next-bold">How does it work?</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="flex max-lg:flex-col gap-16 grow absolute inset-0 overflow-y-auto hidden-scrollbar" :style="'height: ' + height + 'px;'">
            <div class="flex-1 grow flex flex-col gap-8">
                <div class="text-center text-2xl py-1 font-avenir-next-rounded-semibold bg-black/60 rounded-full w-full uppercase">Paint in our Cafe</div>
                <ol class="list-decimal list-inside space-y-6 text-xl">
                    <li>
                        Book a Table: Reserve your spot by clicking on the ‘Book a Table’ link. Choose a date and time that works best for you.
                    </li>
                    <li>
                        Come and Paint: Show up at your booked time and select from a variety of pottery pieces. We provide all the paints and tools you need.
                    </li>
                    <li>
                        Leave It to Us: Once you’re done painting, leave your masterpiece with us. We’ll glaze and fire it in our kiln.
                    </li>
                    <li>
                        Pick Up: We’ll notify you when your pottery is ready to be picked up. Come and collect your finished piece and enjoy.
                    </li>
                </ol>
                <a href="book-table" wire:navigate class="uppercase mt-auto px-6 py-2 rounded-full bg-yellow-500 mx-auto text-xl text-black">Book Now</a>
            </div>
            <div class="flex-1 grow flex flex-col gap-8">
                <div class="text-center text-2xl py-1 font-avenir-next-rounded-semibold bg-black/60 rounded-full w-full uppercase">Paint at Home</div>
                <ol class="list-decimal list-inside space-y-6 text-xl">
                    <li>
                        Order Your Kit: Visit our ‘Shop’ tab to order your DIY pottery painting kit. Each kit includes everything you need to paint at home.
                    </li>
                    <li>
                        Paint at Your Leisure: Take your time and paint your pottery with the colors and designs of your choice.
                    </li>
                    <li>
                        Drop It Off: Once you’re finished, bring your pottery back to our shop for firing.
                    </li>
                    <li>
                        We Fire It: We’ll take care of the glazing and firing.
                    </li>
                    <li>
                        Pick Up: We’ll let you know when your pottery is ready to be picked up. Come and collect your handiwork!
                    </li>
                </ol>
                <a href="shop" wire:navigate class="uppercase mt-auto px-6 py-2 rounded-full bg-yellow-500 mx-auto text-xl text-black">Shop Now</a>
            </div>
        </div>
    </div>
</div>