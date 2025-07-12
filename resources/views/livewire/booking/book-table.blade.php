<?php

use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use App\Models\Booking;
use App\Models\Date;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

use function Livewire\Volt\{state, mount};

state('path');

mount(fn($path) => $this->path = $path);
?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="flex max-sm:flex-col sm:justify-between gap-2 items-center">
        <div class="text-5xl lg:text-7xl font-avenir-next-bold text-white">Book a Table</div>
        <livewire:audio-player />
    </div>
    <livewire:form.booking-form :path="$path" />
</div>