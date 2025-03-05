<?php

use function Livewire\Volt\{state};

//

?>

<div class="w-11/12 mx-auto gap-4 lg:gap-8 py-4 lg:py-8 text-white grow flex flex-col">
    <livewire:section.header header="Classes" />
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-y-auto hidden-scrollbar absolute inset-x-0 flex flex-col" :style="'height: ' + height + 'px;'">
            <livewire:section.classes />
        </div>
    </div>
</div>