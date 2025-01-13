<?php

use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{state, mount};

state(['user']);

mount(function () {
    $this->user = Auth::user();
});

?>

<div class="flex justify-between items-center py-6 px-4 border-b border-black/30">
    <div class="text-xl text-black/60 font-medium">Hi, {{$user->first_name}}</div>
    <div>
        <svg class="w-10 h-10 text-black/60" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
    </div>
    <div></div>
</div>