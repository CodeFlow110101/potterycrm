<?php

use App\Models\PostalCode;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{state, with, mount, computed};

state(['user', 'cart' => [], 'shippingType', 'search', 'address']);

with(fn() => ['products' => Product::whereRaw("LOWER(REPLACE(name, ' ', '')) LIKE ?", ['%' . strtolower(str_replace(' ', '', $this->search)) . '%'])->get()]);

$increaseQuantity = function ($id) {
    $this->cart[$id]++;
};

$decreaseQuantity = function ($id) {
    if ($this->cart[$id] - 1 == 0) {
        unset($this->cart[$id]);
    } else {
        $this->cart[$id]--;
    }
};

$addProduct = function ($id) {
    $this->cart[$id] = 1;
};

$removeProduct = function ($id) {
    unset($this->cart[$id]);
};

$totals = computed(function () {
    $subTotal = 0;
    foreach (Product::whereIn('id', array_keys($this->cart))->get() as $product) {
        $subTotal += $product->price * $this->cart[$product->id];
    }
    $discount = $subTotal * 0.05;
    $salesTax = $subTotal * 0.02;
    $total = ($subTotal + $salesTax) - $discount;

    return ['subTotal' => $subTotal, 'discount' => $discount, 'salesTax' => $salesTax, 'total' => $total];
});

mount(function () {
    $this->user = Auth::user();
});
?>

<div x-data="{ show : 'cart' }" class="grow flex justify-between bg-black/5">
    <div class="h-full w-4/6 flex flex-col p-4">
        <div class="py-12 flex justify-between items-center">
            <div class="w-full flex flex-col gap-2">
                <div class="text-2xl font-medium text-black/80">
                    Welcome, {{$user->first_name}}
                </div>
                <div class="text-black/40 text-sm font-medium">Discover Whatever you need easily</div>
            </div>
            <div class="w-full flex justify-end">
                <div class="w-4/5 relative">
                    <input wire:model.live="search" class="w-full bg-white rounded-md outline-none pl-12 py-3 pr-3" placeholder="Search Product ..." />
                    <div class="absolute inset-y-0 flex items-center px-3">
                        <svg class="w-6 h-6 text-black/40" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-6 py-4 overflow-y-auto max-h-[75vh] mb-auto">
            <a href="/manage-product" wire:navigate class="w-full h-[40vh] flex flex-col rounded-lg border shadow-lg border-black/30 p-6">
                <div class="m-auto text-right text-2xl">
                    <svg class="w-12 h-12 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                    </svg>
                </div>
            </a>
            @foreach($products as $product)
            <div class="w-full h-[40vh] bg-cover bg-center flex flex-col gap-2 rounded-lg border shadow-lg border-black/30 p-3">
                <div class="w-full h-1/2 relative">
                    <img class="size-full rounded-lg" src="{{asset('storage/'.$product->thumbnail_path)}}">
                    <div x-show="show == 'cart'" class="absolute inset-0 p-2 flex justify-end">
                        @if(array_key_exists($product->id, $cart))
                        <button wire:click="removeProduct({{$product->id}})" class="size-min rounded-md bg-white p-1 opacity-60">
                            <svg class="w-6 h-6 text-amber-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14" />
                            </svg>
                        </button>
                        @else
                        <button wire:click="addProduct({{$product->id}})" class="size-min rounded-md bg-white p-1 opacity-60">
                            <svg class="w-6 h-6 text-amber-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14m-7 7V5" />
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>
                <div class="grow flex flex-col py-2 text-xl">
                    <div class="text-black/80">
                        {{$product->name}}
                    </div>
                    <div class="mt-auto text-amber-500 font-medium">${{$product->price}}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="w-2/6 h-full flex flex-col gap-4 bg-white px-8 pb-8 pt-16">
        <div>
            <div x-show="show == 'cart'" class="text-2xl font-medium text-black/60">Cart</div>
            <div x-show="show == 'address'" class="text-2xl font-medium text-black/60">Select Shipping Preference</div>
        </div>
        <div x-show="show == 'cart'" class="max-h-[42vh] overflow-y-auto my-auto rounded-xl p-2">
            <div class="flex flex-col gap-5">
                @if(count($cart) == 0)
                <div class="text-center font-semibold text-black/40 text-sm">There are no items in items in your Cart.</div>
                @else
                @foreach($products as $product)
                @if(array_key_exists($product->id, $cart))
                <div class="flex justify-between items-center gap-2 h-[10vh]">
                    <div class="w-1/4 h-full">
                        <img class="h-full w-full rounded-lg" src="{{asset('storage/'.$product->thumbnail_path)}}">
                    </div>
                    <div class="w-3/4 h-full flex flex-col">
                        <div class="text-lg text-black/60">{{$product->name}}</div>
                        <div class="flex justify-between mt-auto">
                            <div class="text-amber-500 text-lg font-medium">${{$product->price}}</div>
                            <div class="w-min flex justify-evenly items-center gap-3">
                                <button wire:click="increaseQuantity({{$product->id}})" class="bg-amber-500 rounded-md aspect-square py-1 px-2 text-white text-sm">+</button>
                                <div class="text-black/60">{{$cart[$product->id]}}</div>
                                <button wire:click="decreaseQuantity({{$product->id}})" class="bg-amber-500 rounded-md aspect-square py-1 px-2 text-white text-sm">-</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
                @endif
            </div>
        </div>
        <div x-show="show == 'address'" class="mb-auto">
            <div class="flex justify-between items-center gap-4">
                <button @click="$refs.pickupRadioButton.click()" :class="$wire.shippingType == 0 ? 'border-blue-500' : 'border-black/60'" class="w-full flex justify-start items-center gap-4 border rounded-lg py-2 px-3">
                    <input class="hidden" x-ref="pickupRadioButton" type="radio" value="0" wire:model="shippingType">
                    <div class="p-0.5 rounded-full border border-black/30">
                        <div :class="$wire.shippingType == 0 && 'bg-blue-500'" class="rounded-full size-3"></div>
                    </div>
                    <div class="font-semibold text-black/60">Pickup</div>
                </button>
                <button @click="$refs.deliverRadioButton.click()" :class="$wire.shippingType == 1 ? 'border-blue-500' : 'border-black/60'" class="w-full flex justify-start items-center gap-4 border rounded-lg py-2 px-3">
                    <input class="hidden" x-ref="deliverRadioButton" type="radio" value="1" wire:model="shippingType">
                    <div class="p-0.5 rounded-full border border-black/30">
                        <div :class="$wire.shippingType == 1 && 'bg-blue-500'" class="rounded-full  size-3"></div>
                    </div>
                    <div class="font-semibold text-black/60">Deliver</div>
                </button>
            </div>
            <div class="py-6">
                <div x-show="$wire.shippingType == 1" class="flex flex-col gap-2">
                    <div class="text-black/60 font-semibold">Select an Address</div>
                    <!-- <div class="overflow-y-auto max-h-[50vh] flex flex-col gap-4">
                        <button @click="$refs.kalamboli.click()" :class="$wire.address == 1 ? 'border-blue-500' : 'border-black/30'" class="border-2 flex flex-col rounded-md p-2">
                            <div class="font-medium">Kalamboli</div>
                            <div class="text-sm text-black/60">A-402,Vision Residency...</div>
                            <input class="hidden" x-ref="kalamboli" type="radio" value="1" wire:model="address">
                        </button>
                        <button @click="$refs.pune.click()" :class="$wire.address == 2 ? 'border-blue-500' : 'border-black/30'" class="border-2 border-black/30 flex flex-col rounded-md p-2">
                            <div class="font-medium">Pune</div>
                            <div class="text-sm text-black/60">Boat club Road...</div>
                            <input class="hidden" x-ref="pune" type="radio" value="2" wire:model="address">
                        </button>
                        <button @click="$refs.pune2.click()" :class="$wire.address == 3 ? 'border-blue-500' : 'border-black/30'" class="border-2 border-black/30 flex flex-col rounded-md p-2">
                            <div class="font-medium">Pune</div>
                            <div class="text-sm text-black/60">Salisbury Park...</div>
                            <input class="hidden" x-ref="pune2" type="radio" value="3" wire:model="address">
                        </button>
                    </div> -->
                </div>
            </div>
        </div>
        <div class="mt-auto flex flex-col gap-4">
            <div x-show="show == 'cart'" class="bg-black/5 rounded-lg">
                <div class="flex flex-col gap-3 p-6 capitalize">
                    <div class="flex justify-between items-center">
                        <div class="text-black/50">Subotal</div>
                        <div>${{$this->totals['subTotal']}}</div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="text-black/50">discount</div>
                        <div>-${{$this->totals['discount']}}</div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="text-black/50">sales tax</div>
                        <div>${{$this->totals['salesTax']}}</div>
                    </div>
                </div>
                <div class="relative h-6 flex items-center">
                    <div class="absolute inset-0 flex justify-between">
                        <div class="bg-white h-full aspect-square -translate-x-1/2 rounded-full"></div>
                        <div class="bg-white h-full aspect-square translate-x-1/2 rounded-full"></div>
                    </div>
                    <div class="border border-dashed border-black/50 w-full h-0"></div>
                </div>
                <div class="flex justify-between items-center p-4 text-xl">
                    <div>Total</div>
                    <div>${{$this->totals['total']}}</div>
                </div>
            </div>
        </div>
        <div class="flex justify-between items-center gap-4">
            <button x-cloak x-show="show == 'address'" @click="show = 'cart'" class="bg-amber-500 font-medium w-min text-center p-3 text-lg rounded-md text-white">
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4" />
                </svg>
            </button>
            <button @click="show = 'address'" :class="$wire.cart.length == 0 && 'pointer-events-none opacity-60'" class="bg-amber-500 font-medium w-full text-center p-3 text-lg rounded-md text-white">Continue to Payment</button>
        </div>
    </div>
</div>