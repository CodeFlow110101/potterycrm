<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use function Livewire\Volt\{mount, state, with, on};

with(fn() => ['user' => User::with(['addresses'])->find(Auth::user()->id)]);

on(['reset' => function () {
    $this->reset();
}]);
?>

<div class="grow flex flex-col bg-black/5 p-4">
    <div class="py-12 flex justify-between items-center">
        <div class="w-full flex flex-col gap-2">
            <div class="text-2xl font-medium text-black/80">
                Welcome, {{$user->first_name}}
            </div>
            <div class="text-black/40 text-sm font-medium">Discover Whatever you need easily</div>
        </div>
    </div>
    <div class="grow flex justify-between">
        <div class="w-full"></div>
        <div class="size-full flex flex-col gap-4 border border-black/30 rounded-xl p-4">
            <div class="font-semibold text-lg text-black/60">Addresses</div>
            <div class="rounded-xl border border-black/30 grow p-4 flex flex-col gap-4">
                <button @click="$dispatch('show-modal', { name: 'manage-address', data: null })" class="border border-black/30 py-4 rounded-md w-full">
                    <svg class="w-8 h-8 text-black/30 mx-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                    </svg>
                </button>
                <div class="max-h-[50vh] flex flex-col gap-4 overflow-y-auto">
                    @foreach($user->addresses as $address)
                    <div class="border border-black/30 py-2 rounded-md w-full p-4 flex flex-col gap-1">
                        <div>{{$address->name}}</div>
                        <div>{{Str::limit($address->address, 50)}}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>