<?php

use function Livewire\Volt\{state};

//

?>

<div class="w-11/12 mx-auto gap-8 py-8 text-white grow flex flex-col">
    <div class="text-7xl font-avenir-next-bold">About Us</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="backdrop-blur-xl border border-white rounded-lg overflow-y-auto hidden-scrollbar absolute inset-x-0 flex flex-col" :style="'height: ' + height + 'px;'">
            <div class="text-xl flex flex-col gap-4 w-11/12 mx-auto py-8 grow">
                <div>
                    Welcome to ICONA, a creative space where art meets imagination!
                </div>
                <div>
                    Our shop is dedicated to providing a relaxing, fun, and artistic environment where anyone can create something beautiful. Whether you're a beginner or a seasoned artist, we offer a unique opportunity to explore your artistic side through pottery painting.
                </div>
                <div>
                    Our commitment is to provide an inspiring space, high-quality materials, and the guidance needed to spark your creativity. </div>
            </div>
        </div>
    </div>
</div>