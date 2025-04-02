<?php

use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use function Livewire\Volt\{state, rules, on, with, mount};

state(['name', 'description', 'thumbnail', 'price', 'type', 'product', 'preview', 'can_update_product']);

rules(fn() => [
    'name' => ['required', 'min:3'],
    'description' => ['required', 'min:6'],
    'price' => ['required', function ($attribute, $value, $fail) {
        preg_match('/^\d+\.\d{2}$/', $value) || $fail('The :attribute should be in this format $ 99.99.');
    },],
    'thumbnail' => $this->product ? ['exclude'] : ['required', 'lt:100'],
    'can_update_product' => $this->product ?  [
        function ($attribute, $value, $fail) {
            User::whereHas('purchases.items.product', function (Builder $query) {
                $query->where('id', $this->product->id);
            })->exists() && $fail('The product have some purchases please prefer to delete and add a new one.');
        },
    ] : ['exclude'],
    'type' => ['required'],
])->messages([
    'thumbnail.lt' => 'The :attribute must be less than 100kb.',
]);


with(fn() => ['producttypes' => ProductType::get()]);

on(['store' => function ($file) {

    Product::create([
        'name' => $this->name,
        'description' => $this->description,
        'price' => str_replace('.', '', $this->price),
        'thumbnail' => $file['name'],
        'thumbnail_path' => $file['path'],
        'type_id' => $this->type,
    ]);

    $this->reset();
    $this->dispatch('reset-file-input');
    $this->dispatch('loader', show: false);
    $this->redirect('/shop', navigate: true);
}]);

$submit = function () {
    $this->validate();

    $this->product && Product::find($this->product->id)?->update([
        'name' => $this->name,
        'description' => $this->description,
        'price' => str_replace('.', '', $this->price),
        'type_id' => $this->type,
    ]);

    $this->product && $this->redirect('/shop', navigate: true);


    $this->product || $this->dispatch('file-upload');
};

$delete = function () {
    $this->product->delete();
    $this->redirect('/shop', navigate: true);
};

mount(function (Request $request) {
    $this->product = Product::find($request->route('id'));
    if ($this->product) {
        $this->name = $this->product->name;
        $this->description = $this->product->description;
        $this->thumbnail = $this->product->thumbnail_path;
        $this->price = $this->product->price / 100;
        $this->type = $this->product->type_id;
        $this->preview = asset('storage/' . $this->product->thumbnail_path);
    }
});

?>

<div x-data="imageUploader" class="w-11/12 mx-auto gap-8 py-8 text-white grow flex flex-col">
    <form wire:submit="submit" class="grow flex flex-col gap-8">
        <div class="flex justify-between items-center">
            <div class="text-7xl font-avenir-next-bold text-white">Bookings</div>
            <div class="flex flex-col gap-2 items-end">
                <div class="flex items-center gap-3">
                    @if($this->product)
                    <button type="button" wire:click="delete" class="text-black py-3 uppercase px-6 bg-white rounded-lg tracking-tight">
                        Delete
                    </button>
                    @endif
                    <a href="/shop" wire:navigate class="text-black py-3 uppercase px-6 bg-white rounded-lg tracking-tight">Cancel</a>
                    <button type="submit" class="text-black py-3 uppercase px-6 bg-white rounded-lg tracking-tight">
                        Save
                    </button>
                </div>
                @error('can_update_product')
                <span wire:transition.in.duration.500ms="scale-y-100"
                    wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="grow flex flex-col relative " x-data="{ height: 0 }" x-resize="height = $height">
            <div class="grow flex flex-col gap-4 absolute inset-0 overflow-y-auto">
                <div class="grow flex flex-col w-full">
                    <div class="flex justify-between grow gap-4">
                        <div class="grow flex flex-col w-2/5">
                            <div class="grow flex flex-col gap-4">
                                <div class="h-1/2 flex flex-col gap-4 border border-white backdrop-blur-xl hidden-scrollbar rounded-lg shadow-lg p-4 @if($product) pointer-events-none @endif">
                                    <div class="text-xl text-white">Thumbnail</div>
                                    <div x-show="!preview"
                                        x-transition:enter="transition ease-out duration-500"
                                        x-transition:enter-start="opacity-0 scale-90"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        @click="$refs.imageInput.click()" class="grow border border-white rounded-lg flex justify-center items-center">
                                        <svg class="w-16 h-16 text-blackwhite" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
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
                                            wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="grow border border-white backdrop-blur-xl rounded-lg shadow-lg p-4">
                                    <div class="h-min grid grid-cols-1 gap-4">
                                        <div>
                                            <div class="relative">
                                                <input wire:model="price" x-mask:dynamic="validatePriceFormat($input)"
                                                    class="w-full bg-black/20 outline-none p-3 pl-8 placeholder:text-white/80 " placeholder="99.99">
                                                <div class="absolute inset-y-0 flex items-center w-min px-2">
                                                    <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M8 17.345a4.76 4.76 0 0 0 2.558 1.618c2.274.589 4.512-.446 4.999-2.31.487-1.866-1.273-3.9-3.546-4.49-2.273-.59-4.034-2.623-3.547-4.488.486-1.865 2.724-2.899 4.998-2.31.982.236 1.87.793 2.538 1.592m-3.879 12.171V21m0-18v2.2" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                @error('price')
                                                <span wire:transition.in.duration.500ms="scale-y-100"
                                                    wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="grow flex flex-col w-3/5">
                            <div class="grow border border-white backdrop-blur-xl rounded-lg shadow-lg p-4">
                                <div class="text-xl text-white border-b border-white py-4">Details</div>
                                <div class="h-min grid grid-cols-1 gap-12 py-8">
                                    <div>
                                        <input wire:model="name" class="w-full bg-black/20 outline-none p-3 placeholder:text-white/80" placeholder="Name">
                                        <div>
                                            @error('name')
                                            <span wire:transition.in.duration.500ms="scale-y-100"
                                                wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <input wire:model="description" class="w-full bg-black/20 outline-none p-3 placeholder:text-white/80" placeholder="Description">
                                        <div>
                                            @error('description')
                                            <span wire:transition.in.duration.500ms="scale-y-100"
                                                wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <select wire:model="type" class="w-full bg-black/20 outline-none p-3 placeholder:text-white/80 capitalize">
                                            <option value="">Select a Product Type</option>
                                            @foreach($producttypes as $type)
                                            <option value="{{$type->id}}">{{$type->name}}</option>
                                            @endforeach
                                        </select>
                                        <div>
                                            @error('type')
                                            <span wire:transition.in.duration.500ms="scale-y-100"
                                                wire:transition.out.duration.500ms="scale-y-0" class="text-white">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>