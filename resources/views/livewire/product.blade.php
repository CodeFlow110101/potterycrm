<?php

use App\Models\Product;
use Illuminate\Http\Request;

use function Livewire\Volt\{state, mount};

state(['product', 'quantity' => 1]);

$submit = function () {
    $this->dispatch('add-cart', id: $this->product->id, quantity: $this->quantity);
};

mount(function (Request $request) {
    $this->product = Product::find($request->route('id'));
});
?>

<div>
    <div class="flex gap-32 my-32 w-3/4 mx-auto">
        <div class="w-full">
            <img class="w-full aspect-square" src="{{asset('storage/'.$product->thumbnail_path)}}">
        </div>
        <form wire:submit.prevent="submit" class="w-full">
            <div class="flex flex-col gap-6 text-primary h-full">
                <div class="flex flex-col">
                    <div class="font-avenir-next-regular text-3xl tracking-wider">
                        {{ $product->name }}
                    </div>
                    <div class="text-lg font-avenir-next-regular tracking-wider">
                        {{ $product->description }}
                    </div>
                    <div class="text-lg font-avenir-next-regular tracking-wider">
                        ${{ $product->price }}
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label>quantity</label>
                    <input x-mask="99" @input="if ($event.target.value.trim() === '' || $event.target.value.trim() === '00' || $event.target.value.trim() === '0') $event.target.value = 1" wire:model="quantity" class="h-14 w-20 text-center border border-primary outline-none">
                </div>
                <div class="mt-auto">
                    <button class="bg-primary bg-opacity-90 hover:bg-opacity-100 text-white py-3 w-full uppercase font-avenir-next-rounded-extra-light tracking-wider">Add to Cart</button>
                </div>
            </div>
        </form>
    </div>
</div>