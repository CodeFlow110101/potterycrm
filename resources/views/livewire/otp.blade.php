<?php

use App\Models\Otp;

use function Livewire\Volt\{state, with};

with(fn() => ['otps' => Otp::latest()->first()->otp]);

?>

<div>
    {{$otps}}
</div>