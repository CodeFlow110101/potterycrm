<?php

use function Livewire\Volt\{state};

//

?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <livewire:section.header header="How does it work?" />
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="grow absolute inset-0 overflow-y-auto hidden-scrollbar" :style="'height: ' + height + 'px;'">
            <livewire:section.how-it-works />
        </div>
    </div>
</div>