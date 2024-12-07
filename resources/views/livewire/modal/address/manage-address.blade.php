<?php

use App\Models\PostCode;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{state, mount, rules};

state(['name', 'address', 'postcode']);

rules(['name' => 'required|unique:addresses,name,NULL,id,user_id,' . Auth::user()->id, 'address' => 'required|min:6', 'postcode' => 'required|exists:postal_codes'])->attributes(['name'=>'address name']);

$submit = function () {
    $this->validate();
    Auth::user()->addresses()->create([
        'name' => $this->name,
        'address' => $this->address,
        'postcode_id' => PostCode::where('postcode', $this->postcode)->first()->id,
    ]);
    $this->dispatch('reset');
    $this->dispatch('hide-modal');
};

mount(function ($data) {});
?>

<div class="h-full flex flex-col p-6 gap-6">
    <div class="flex justify-between items-center border-b border-black/30 pb-2">
        <div class="text-xl text-black/60 font-semibold">Add Address</div>
        <button @click="$dispatch('hide-modal')" class="size-min p-1 group hover:bg-black/30 rounded-full transition-colors duration-200">
            <svg class="w-6 h-6 text-black/30 group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
            </svg>
        </button>
    </div>
    <form wire:submit="submit" class="flex flex-col justify-around gap-5">
        <div>
            <input wire:model="name" class="p-2 border border-amber-500 outline-none w-full rounded-md placeholder:text-amber-500/70" placeholder="Address Name">
            @error('name')
            <div class="text-sm text-red-500">{{$message}}</div>
            @enderror
        </div>
        <div>
            <textarea wire:model="address" class="p-2 border border-amber-500 outline-none w-full rounded-md placeholder:text-amber-500/70" placeholder="Address"></textarea>
            @error('address')
            <div class="text-sm text-red-500">{{$message}}</div>
            @enderror
        </div>
        <div>
            <input wire:model="postcode" x-mask="9999" class="p-2 border border-amber-500 outline-none w-full rounded-md placeholder:text-amber-500/70" placeholder="Postcode">
            @error('postcode')
            <div class="text-sm text-red-500">{{$message}}</div>
            @enderror
        </div>
        <button type="submit" class="py-2 px-4 bg-amber-500 rounded-md text-white font-semibold w-min mx-auto">Submit</button>
    </form>
</div>