<?php

use App\Models\Product;
use App\Models\ProductType;

use function Livewire\Volt\{state, rules, on, with};

state(['name', 'description', 'thumbnail', 'price', 'type']);

rules(['name' => 'required|min:3', 'description' => 'required|min:6', 'price' => 'required', 'thumbnail' => "required|lt:100", 'type' => "required"])->messages([
    'thumbnail.lt' => 'The :attribute must be less than 100kb.',
]);

with(fn() => ['producttypes' => ProductType::get()]);

on(['store' => function ($file) {

    Product::create([
        'name' => $this->name,
        'description' => $this->description,
        'price' => $this->price,
        'thumbnail' => $file['name'],
        'thumbnail_path' => $file['path'],
        'type_id' => $this->type,
    ]);

    $this->reset();
    $this->dispatch('reset-file-input');
    $this->dispatch('loader', show: false);
    $this->redirectRoute('shop', navigate: true);
}]);

$submit = function () {
    $this->validate();
    $this->dispatch('file-upload');
};

?>

<div x-data="imageUploader" class="grow flex flex-col">
    <form wire:submit="submit" class="p-4 grow flex flex-col gap-4">
        <div class="flex justify-between items-center px-2">
            <div class="text-2xl font-semibold capitalize text-black/60">
                add a new product
            </div>
            <div x-data="{loader : false}" x-on:loader.window="loader = $event.detail.show;" class="w-min flex justify-between gap-4">
                <a href="/product" wire:navigate class="rounded-full border border-black/30 px-4 py-2">Cancel</a>
                <button :class="loader && 'pointer-events-none'" wire:loading.class="pointer-events-none" wire:target="submit" type="submit" class="rounded-full bg-blue-500 text-white px-4 py-2">
                    <div :class="loader && 'hidden'" wire:loading.class="hidden pointer-events-none" wire:target="submit">Save</div>
                    <div wire:loading.class.remove="hidden" wire:target="submit" class="mx-4 hidden">
                        <svg aria-hidden="true" class="w-6 h-6 text-transparent animate-spin fill-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                        </svg>
                    </div>
                    <div :class="loader || 'hidden'" class="mx-4">
                        <svg aria-hidden="true" class="w-6 h-6 text-transparent animate-spin fill-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                        </svg>
                    </div>
                </button>
            </div>
        </div>
        <div class="grow flex flex-col w-full">
            <div class="flex justify-between grow gap-4">
                <div class="grow flex flex-col w-2/5">
                    <div class="grow flex flex-col gap-4">
                        <div class="h-1/2 flex flex-col gap-4 border border-black/30 rounded-3xl shadow-lg p-4">
                            <div class="text-xl text-black/60">Thumbnail</div>
                            <div x-show="!preview"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                @click="$refs.imageInput.click()" class="grow border border-black/30 rounded-2xl flex justify-center items-center">
                                <svg class="w-16 h-16 text-black/30" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3 16 5-7 6 6.5m6.5 2.5L16 13l-4.286 6M14 10h.01M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z" />
                                </svg>
                            </div>
                            <div x-show="preview"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                class="cursor-pointer" @click="$refs.imageInput.click()">
                                <img class="rounded-2xl size-full" :src="preview" alt="Image Preview">
                                <input x-on:reset-file-input.window="$refs.imageInput.value = null; $refs.imageInput.dispatchEvent(new Event('change'));" class="hidden" type="file" x-ref="imageInput" id="file" @change="previewImage" accept="image/*" />
                            </div>
                            <input class="hidden" wire:model="thumbnail" type="text">
                            <div>
                                @error('thumbnail')
                                <span wire:transition.in.duration.500ms="scale-y-100"
                                    wire:transition.out.duration.500ms="scale-y-0" class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="grow border border-black/30 rounded-3xl shadow-lg p-4">
                            <div class="h-min grid grid-cols-1 gap-4">
                                <div>
                                    <div class="relative">
                                        <input wire:model="price" x-mask="99999999" class="w-full pl-6 py-2 pr-2 border border-black/30 rounded-lg outline-none text-black/60 font-semibold" placeholder="Price">
                                        <div class="absolute inset-y-0 flex items-center w-min px-2">
                                            <svg class="w-4 h-4 text-black/60" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        @error('price')
                                        <span wire:transition.in.duration.500ms="scale-y-100"
                                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-500">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grow flex flex-col w-3/5">
                    <div class="grow border border-black/30 rounded-3xl shadow-lg p-4">
                        <div class="text-xl text-black/60 border-b border-black/30 py-4">Details</div>
                        <div class="h-min grid grid-cols-1 gap-12 py-8">
                            <div>
                                <input wire:model="name" class="w-full border border-black/30 rounded-lg outline-none p-2 text-black/60 font-semibold" placeholder="Name">
                                <div>
                                    @error('name')
                                    <span wire:transition.in.duration.500ms="scale-y-100"
                                        wire:transition.out.duration.500ms="scale-y-0" class="text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <input wire:model="description" class="w-full border border-black/30 rounded-lg outline-none p-2 text-black/60 font-semibold" placeholder="Description">
                                <div>
                                    @error('description')
                                    <span wire:transition.in.duration.500ms="scale-y-100"
                                        wire:transition.out.duration.500ms="scale-y-0" class="text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div>
                                <select wire:model="type" class="w-full border border-black/30 rounded-lg outline-none p-2 text-black/60 font-semibold capitalize">
                                    <option value="">Select a Product Type</option>
                                    @foreach($producttypes as $type)
                                    <option value="{{$type->id}}">{{$type->name}}</option>
                                    @endforeach
                                </select>
                                <div>
                                    @error('type')
                                    <span wire:transition.in.duration.500ms="scale-y-100"
                                        wire:transition.out.duration.500ms="scale-y-0" class="text-red-500">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>