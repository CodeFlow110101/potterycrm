<?php

use App\Models\Product;

use function Livewire\Volt\{state, with, mount};

state(['cart'])->reactive();

with(fn() => ['products' => Product::whereIn('id', array_keys($this->cart))->when($this->booking_id, function ($query) {
    $query->whereHas('type', function ($typeQuery) {
        $typeQuery->where('name', 'in store');
    });
}, function ($query) {
    $query->whereHas('type', function ($typeQuery) {
        $typeQuery->where('name', 'online');
    });
})->get()]);

$updateCart = function ($id, $quantity) {
    $this->dispatch('update-cart', id: $id, quantity: $quantity);
};

$removeCart = function ($id) {
    $this->dispatch('remove-cart', id: $id);
};

mount(function () {
    $this->booking_id = request()->route('booking_id');
});
?>

<div class="my-32 text-primary">
    <div class="flex flex-col gap-4 text-center">
        <div class="text-3xl font-avenir-next-rounded-light">
            Your Cart
        </div>
        @if(count($cart) == 0)
        <div class="text-center font-avenir-next-rounded-light">
            Your cart is currently empty.
        </div>
        @endif
        <a href="/shop" wire:navigate class="font-avenir-next-rounded-light uppercase underline-offset-4 w-min whitespace-nowrap mx-auto flex justify-around gap-2 @if(count($cart) == 0) text-white bg-opacity-90 hover:bg-opacity-100 bg-primary py-2 px-4 tracking-wider @else underline @endif">
            <div>Continue shopping</div>
            @if(count($cart) == 0)
            <div>
                <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                </svg>
            </div>
            @endif
        </a>
    </div>
    @if(count($cart) != 0)
    <table class="w-3/4 mx-auto my-16 font-avenir-next-rounded-regular">
        <thead>
            <tr class="border-b">
                <th class="text-left py-6">Product</th>
                <th class="py-6">Price</th>
                <th class="py-6">Quantity</th>
                <th class="py-6">Total</th>
            </tr>
        </thead>
        <tbody class="text-center">
            @foreach($products as $product)
            <tr class="border-b">
                <td class="flex justify-start py-12">
                    <div class="flex justify-start gap-4">
                        <div>
                            <img class="w-36 aspect-square" src="{{asset('storage/'.$product->thumbnail_path)}}">
                        </div>
                        <div class="flex flex-col gap-2 text-left text-xl">
                            <div>{{ $product->name }}</div>
                            <div>{{ $product->description }}</div>
                            <button wire:click="removeCart({{ $product->id }} )" class="underline mt-auto text-left text-base">Remove</button>
                        </div>
                    </div>
                </td>
                <td class="py-12">$ {{ $product->price }}</td>
                <td class="py-12">
                    <input x-mask="99" @input="if ($event.target.value.trim() === '' || $event.target.value.trim() === '00' || $event.target.value.trim() === '0') $event.target.value = 0" wire:change="updateCart({{ $product->id }} , $event.target.value)" value="{{ $cart[$product->id] }}" class="h-14 w-20 text-center border border-primary outline-none">
                </td>
                <td>$ {{ $product->price * $cart[$product->id]}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="w-3/4 mx-auto flex justify-end">
        <a href="/checkout" wire:navigate class="bg-primary bg-opacity-90 hover:bg-opacity-100 text-white py-3 px-6 uppercase font-avenir-next-rounded-extra-light tracking-wider">Checkout</a>
    </div>
    @endif
</div>