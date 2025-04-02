<?php

use App\Models\Product;

use function Livewire\Volt\{state, with, mount};

state(['cart'])->reactive();

with(fn() => ['products' => Product::whereIn('id', array_keys($this->cart))->get()]);

$updateCart = function ($id, $quantity) {
    $this->dispatch('update-cart', id: $id, quantity: $quantity);
};

$removeCart = function ($id) {
    $this->dispatch('remove-cart', id: $id);
};

?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="text-5xl lg:text-7xl font-avenir-next-bold">Cart</div>
    <div class="grow backdrop-blur-xl border border-white rounded-lg py-8 flex flex-col gap-6">
        <div class="flex flex-col gap-4 text-center my-auto">
            @if(count($cart) == 0)
            <div class="text-center font-avenir-next-rounded-semibold text-lg">
                Your cart is currently empty.
            </div>
            <a href="/shop" wire:navigate class="text-black py-3 uppercase px-6 bg-white rounded-lg tracking-tight w-min whitespace-nowrap mx-auto flex items-center gap-2">
                <div>Continue shopping</div>
                <div>
                    <svg class="w-6 h-6 text-" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                    </svg>
                </div>
            </a>
            @endif
        </div>
        @if(count($cart) != 0)
        <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
            <div class="absolute inset-x-0 overflow-y-auto overflow-x-auto hidden-scrollbar" :style="'height: ' + height + 'px;'">
                <table class="w-11/12 mx-auto table-auto text-center">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-6">Product</th>
                            <th class="py-6 max-sm:hidden">Price</th>
                            <th class="py-6 max-sm:hidden">Quantity</th>
                            <th class="py-6">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr class="border-b">
                            <td class="flex justify-start py-12">
                                <div class="flex max-sm:flex-col justify-start gap-4">
                                    <div>
                                        <img class="w-36 aspect-square" src="{{asset('storage/'.$product->thumbnail_path)}}">
                                    </div>
                                    <div class="flex items-center gap-4 sm:hidden">
                                        <div class="text-xl">$ {{ $product->price }}</div>
                                        <div>
                                            <input x-mask="99" @input="if ($event.target.value.trim() === '' || $event.target.value.trim() === '00' || $event.target.value.trim() === '0') $event.target.value = 0" wire:change="updateCart({{ $product->id }} , $event.target.value)" value="{{ $cart[$product->id] }}" class="h-10 w-14 text-center border text-black outline-none">
                                        </div>
                                    </div>
                                    <div class="flex flex-col gap-2 text-left text-xl">
                                        <div>{{ $product->name }}</div>
                                        <div>{{ Str::limit($product->description,20) }}</div>
                                        <button wire:click="removeCart({{ $product->id }} )" class="underline mt-auto text-left text-base">Remove</button>
                                    </div>
                                </div>
                            </td>
                            <td class="py-12 max-sm:hidden">$ {{ $product->price / 100}}</td>
                            <td class="py-12 max-sm:hidden">
                                <input x-mask="99" @input="if ($event.target.value.trim() === '' || $event.target.value.trim() === '00' || $event.target.value.trim() === '0') $event.target.value = 0" wire:change="updateCart({{ $product->id }} , $event.target.value)" value="{{ $cart[$product->id] }}" class="h-14 w-20 text-center border text-black outline-none">
                            </td>
                            <td>$ {{ $product->price * $cart[$product->id] / 100}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="w-11/12 mx-auto flex justify-end">
            <a href="/checkout" wire:navigate class="text-black py-3 uppercase px-6 flex gap-2 bg-white rounded-lg tracking-tight">
                <div>
                    Checkout
                </div>
                <div>
                    <svg class="w-6 h-6 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                    </svg>
                </div>
            </a>
        </div>
        @endif
    </div>
</div>