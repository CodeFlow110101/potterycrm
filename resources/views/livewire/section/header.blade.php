<?php

use function Livewire\Volt\{mount, state};

state(['header']);

mount(function ($header) {
    $this->header = $header;
});
?>


<div class="text-5xl lg:text-7xl font-avenir-next-bold">{{ $header }}</div>