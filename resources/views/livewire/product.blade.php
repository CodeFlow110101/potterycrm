<?php

use App\Models\Product;
use Illuminate\Http\Request;

use function Livewire\Volt\{state, mount, rules};

state(['product', 'quantity' => 1]);

rules(['quantity' => 'required']);

$submit = function () {
    $this->validate();
    $this->dispatch('add-cart', id: $this->product->id, quantity: $this->quantity ? $this->quantity : (string)1);
};

mount(function (Request $request) {
    $this->product = Product::find($request->route('id'));
});
?>

<div class="grow flex flex-col gap-8 py-8 text-white w-11/12 mx-auto">
    <div class="flex justify-between items-center">
        <div class="text-5xl lg:text-7xl font-avenir-next-bold">Product</div>
        <a href="shop" wire:navigate class="text-black py-2 sm:py-3 uppercase px-4 sm:px-6 bg-white rounded-lg tracking-tight flex items-center gap-2 max-sm:text-sm sm:gap-4">
            <div>
                <svg class="w-6 h-6 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4" />
                </svg>
            </div>
            <div>
                Back
            </div>
        </a>
    </div>
    <div class="grow relative flex flex-col" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="grow flex max-sm:flex-col backdrop-blur-xl p-6 border border-white rounded-lg gap-12 lg:gap-24 overflow-y-auto hidden-scrollbar absolute inset-x-0" :style="'height: ' + height + 'px;'">
            <div class="h-full w-full">
                <img class="size-full rounded-lg" src="{{asset('storage/'.$product->thumbnail_path)}}">
            </div>
            <form wire:submit.prevent="submit" class="h-full w-full">
                <div class="flex flex-col gap-6 text-white h-full">
                    <div class="flex flex-col">
                        <div class="font-avenir-next-regular text-3xl tracking-wider">
                            {{ $product->name }}
                        </div>
                        <div class="text-lg font-avenir-next-regular tracking-wider">
                            {{ $product->description }}
                        </div>
                        <div class="text-lg font-avenir-next-regular tracking-wider">
                            ${{ $product->price / 100 }}
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label>quantity</label>
                        <input x-mask="99" @input="if ($event.target.value.trim() === '00' || $event.target.value.trim() === '0') $event.target.value = 1" wire:model="quantity" class="h-14 w-20 text-center border border-white text-black outline-none">
                        @error('quantity')
                        <div class="text-white text-sm">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="mt-auto">
                        <button class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight w-full">Add to Cart</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>