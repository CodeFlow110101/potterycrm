<?php

use function Livewire\Volt\{state};

//

?>

<div x-data="audioPlayer('{{ asset('audio/theme.mp3') }}')" class="text-black">
        <div class="bg-white rounded-full flex justify-between items-center w-min whitespace-nowrap gap-2 p-2 capitalize">
            <div class="relative px-4">
                <div :class="isPlaying && 'invisible'">Our theme song</div>
                <marquee :class="isPlaying || 'hidden'" scrollamount="2" scrolldelay="100" class="absolute inset-0 flex justify-center items-center">Our theme song</marquee>
            </div>
            <button @click="toggle" class="rounded-full bg-white flex justify-center items-center p-2 border-2 border-black">
                <svg x-show="!isPlaying" class="w-6 h-6 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M8.6 5.2A1 1 0 0 0 7 6v12a1 1 0 0 0 1.6.8l8-6a1 1 0 0 0 0-1.6l-8-6Z" clip-rule="evenodd" />
                </svg>
                <svg x-show="isPlaying" class="w-6 h-6 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M8 5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H8Zm7 0a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1Z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
</div>