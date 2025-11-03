<?php

use function Livewire\Volt\{state};

?>

<div class="w-11/12 mx-auto gap-4 lg:gap-8 py-4 lg:py-8 text-white grow flex flex-col">
    <div class="text-5xl lg:text-7xl font-avenir-next-bold">Our Location</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="backdrop-blur-xl border border-white rounded-lg overflow-y-auto hidden-scrollbar absolute inset-x-0" :style="'height: ' + height + 'px;'">
            <div class="flex flex-col gap-2 p-4">
                <div><span class="font-avenir-next-rounded-bold text-lg">Address</span>: 188 Sir Donald Bradman Dr, Cowandilla SA 5033, Australia</div>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3270.9811900077307!2d138.5608098!3d-34.93200869999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ab0cf7db64f4bc7%3A0x32419dacdaddc12c!2s188%20Sir%20Donald%20Bradman%20Dr%2C%20Cowandilla%20SA%205033%2C%20Australia!5e0!3m2!1sen!2sin!4v1741190608536!5m2!1sen!2sin" class="w-full rounded-lg" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>