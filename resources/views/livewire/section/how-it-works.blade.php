<?php

use function Livewire\Volt\{state};

//

?>

<div class="flex max-lg:flex-col gap-16 ">
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
        <div class="text-2xl">Option 1: Paint and Fire</div>
        <ol class="list-decimal list-inside space-y-6 text-xl">
            <li>
                ⁠Order Your Kit: Visit our ‘Shop’ tab and order your DIY pottery painting kit. Be sure to select the "Fire Option" during your purchase. Each kit includes all the essentials you need to paint at home.
            </li>
            <li>
                ⁠Paint at Your Leisure: At home, take your time to paint your pottery with the colors and designs you prefer.
            </li>
            <li>
                ⁠Drop It Off: Once you've completed your masterpiece, bring it back to our shop for firing.
            </li>
            <li>
                We Fire It: We handle all the glazing and firing, ensuring your pottery turns out perfectly.
            </li>
            <li>
                Pick Up: We’ll notify you when your pottery is ready. Stop by to pick up and enjoy your handiwork!
            </li>
        </ol>

        <div class="text-2xl">Option 2: Paint and Display </div>
        <ol class="list-decimal list-inside space-y-6 text-xl">
            <li>
                Order Your Kit: Choose the 'Non-Fire' option from our ‘Shop’ tab. This kit comes with everything you need, including special paints that do not require firing.
            </li>
            <li>
                ⁠Paint at Your Leisure: Relax and enjoy painting your pottery at home with our decorative paints.
            </li>
            <li>
                Let It Dry: Allow your pottery to fully dry at home—no firing needed.
            </li>
            <li>
                Display It: Place your finished piece around your home or give it as a gift. Enjoy your artwork without any additional steps!
            </li>
        </ol>
        <a href="shop" wire:navigate class="uppercase mt-auto px-6 py-2 rounded-full bg-yellow-500 mx-auto text-xl text-black">Shop Now</a>
    </div>
</div>