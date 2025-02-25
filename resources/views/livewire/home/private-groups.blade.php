<?php

use function Livewire\Volt\{state};

//

?>

<div class="w-11/12 mx-auto gap-4 lg:gap-8 py-4 lg:py-8 text-white grow flex flex-col">
    <div class="text-5xl lg:text-7xl font-avenir-next-bold">Private Groups</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="backdrop-blur-xl border border-white rounded-lg overflow-y-auto hidden-scrollbar absolute inset-x-0 flex flex-col" :style="'height: ' + height + 'px;'">
            <div class="text-xl flex flex-col gap-4 w-11/12 mx-auto py-8 grow">
                <div>
                    Looking for a unique way to celebrate, create team bonds, or just have a special event? Icona offers private group bookings! </div>
                <div>
                    Perfect for corporate events, family gatherings, birthdays, and more, our private sessions provide everything you need to have a memorable and creative experience. Contact us to customise your event and make it truly special with exclusive access to our facilities and personalised instruction. </div>
                <div>
                    Join us and unleash your creativity today!
                </div>
            </div>
        </div>
    </div>
</div>