<?php

use App\Models\Package;
use App\Models\PackageTimeSlot;
use App\Models\TimeSlot;

use function Livewire\Volt\{state, with, rules, on, mount, updated};

state(['modal' => false, 'search', 'package', 'thumbnail', 'name', 'description', 'preview', 'selectedTimeSlots' => collect([]), 'isImageUpdated', 'image', 'image_path']);

with(fn() => [
    'packages' => Package::where('name', 'like', '%' . $this->search . '%')->get(),
    'slots' => TimeSlot::get(),
]);

rules(fn() => [
    'name' => $this->package ? ['required', 'min:3'] : ['required', 'min:3', 'unique:packages,name'],
    'description' => ['required', 'min:6'],
    'thumbnail' => $this->package ? ['exclude'] : ['required', 'lt:100'],
])->messages([
    'thumbnail.lt' => 'The :attribute must be less than 100kb.',
]);

updated(['thumbnail' => fn() => $this->package && $this->isImageUpdated = true]);

$toggleSelectedTimeSlots = function ($id) {
    $this->selectedTimeSlots = $this->selectedTimeSlots->contains($id) ? $this->selectedTimeSlots->reject(fn($value) => $value == $id) : $this->selectedTimeSlots->push($id);
};

on(['store' => function ($file) {

    $this->image = $file['name'];
    $this->image_path = $file['path'];

    $this->package && $this->isImageUpdated && Package::find($this->package->id)?->update([
        'name' => $this->name,
        'description' => $this->description,
        'image' => $this->image,
        'image_path' => $this->image_path
    ]);


    $this->package || $this->package = Package::create([
        'name' => $this->name,
        'description' => $this->description,
        'image' => $this->image,
        'image_path' => $this->image_path,
    ]);

    $this->updateTimeSlots();
    $this->toggleModal();
}]);

$submit = function () {
    $this->validate();

    if ($this->package && !$this->isImageUpdated) {
        Package::find($this->package->id)?->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->updateTimeSlots();
        $this->toggleModal();
    } else if (!$this->package || $this->isImageUpdated) {
        $this->dispatch('file-upload');
    } else {
        $this->toggleModal();
    }
};

$updateTimeSlots = function () {
    $this->package->packageTimeSlots()->delete();
    $this->package->packageTimeSlots()->createMany($this->selectedTimeSlots->map(fn($id) => ['time_slot_id' => $id]));
};

$toggleModal = function ($id = null) {

    $this->package = Package::find($id);

    if ($this->package) {
        $this->name = $this->package->name;
        $this->description = $this->package->description;
        $this->image = $this->package->image_path;
        $this->preview = asset('storage/' . $this->package->image_path);
        $this->selectedTimeSlots = $this->package->timeSlots->pluck('id');
    }

    $this->modal = !$this->modal;
    $this->modal || $this->reset(['thumbnail', 'name', 'description', 'preview', 'isImageUpdated', 'image', 'image_path', 'package']);
    $this->modal || $this->selectedTimeSlots = collect([]);
};
?>


<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="flex justify-between items-center">
        <div class="text-5xl lg:text-7xl font-avenir-next-bold text-white">Packages</div>
        <button wire:click="toggleModal" class="text-black py-3 max-sm:hidden uppercase px-6 font-normal bg-white rounded-lg tracking-tight w-min whitespace-nowrap">Create Package</button>
    </div>
    <div class="flex justify-end">
        <div class="sm:w-1/2 w-full">
            <div class="flex gap-3 items-center px-2.5 py-2.5 w-full text-sm text-white font-semibold backdrop-blur-2xl bg-black/10 rounded-lg border-2 border-white">
                <div>
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input wire:model.live="search" type="text" value="" id="floating_outlined" class="block size-full bg-transparent appearance-none focus:outline-none focus:ring-0 peer placeholder:text-white/70" placeholder="Search" />
            </div>
        </div>
    </div>
    <div class="grow relative whitespace-nowrap" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-auto hidden-scrollbar absolute inset-x-0 border border-white rounded-lg" :style="'height: ' + height + 'px;'">
            <table class="w-full overflow-y-hidden backdrop-blur-xl table-auto">
                <thead class="sticky top-0 text-black rounded-t-lg z-10">
                    <tr class="bg-white *:p-3">
                        <th class="font-normal sticky left-0 bg-white">
                            #
                        </th>
                        <th class="font-normal w-full">
                            Name
                        </th>
                        <th class="font-normal">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($packages as $package)
                    <tr class="hover:bg-black/10 transition-colors duration-200 text-white *:p-3">
                        <td class="text-center font-normal sticky left-0 bg-white text-black">{{$loop->iteration}}</td>
                        <td class="text-center font-normal w-full">{{$package->name}}</td>
                        <td class="text-center font-normal">
                            <button wire:click="toggleModal({{ $package->id }})">
                                <svg class="size-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($modal)
    <div class="fixed inset-0" x-data="imageUploader">
        <div class="size-full flex flex-col justify-center items-center">
            <div class="sm:w-4/5 w-full flex flex-col h-4/5">
                <div class="grow flex flex-col relative border-white">
                    <div class="grow flex flex-col gap-4 " x-data="{ height: 0 }" x-resize="height = $height">
                        <form wire:submit="submit" class="backdrop-blur-3xl shadow-lg border border-white rounded-lg flex flex-col gap-3 p-4 grow absolute inset-0 overflow-y-auto hidden-scrollbar">
                            <div class="flex justify-end items-center">
                                <button type="button" wire:click="toggleModal" class="hover:bg-black/30 rounded-full p-1">
                                    <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                    </svg>
                                </button>
                            </div>
                            <div class="border border-white"></div>
                            <div class="grow flex flex-col w-full">
                                <div class="flex justify-between grow gap-4">
                                    <div class="grow flex flex-col w-2/5 gap-4">
                                        <div class="grow flex flex-col gap-4">
                                            <div class="grow flex flex-col gap-4 border border-white backdrop-blur-xl hidden-scrollbar rounded-lg shadow-lg p-4">
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
                                                    <span class="text-white">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grow border border-white backdrop-blur-xl rounded-lg shadow-lg p-4">
                                            <div class="text-xl text-white border-b border-white py-4">Details</div>
                                            <div class="h-min grid grid-cols-1 gap-12 py-8">
                                                <div>
                                                    <input wire:model="name" class="w-full bg-black/20 outline-none p-3 placeholder:text-white/80" placeholder="Name">
                                                    <div>
                                                        @error('name')
                                                        <span class="text-white">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div>
                                                    <input wire:model="description" class="w-full bg-black/20 outline-none p-3 placeholder:text-white/80" placeholder="Description">
                                                    <div>
                                                        @error('description')
                                                        <span class="text-white">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grow flex flex-col w-3/5">
                                        <div class="grow border border-white backdrop-blur-xl rounded-lg shadow-lg p-4">
                                            <div class="text-xl text-white border-b border-white py-4">Time Slots</div>
                                            <div class="flex flex-wrap justify-around gap-4 text-sm py-8">
                                                @foreach($slots as $slot)
                                                <button type="button" wire:click="toggleSelectedTimeSlots({{$slot->id}})" class="@if($selectedTimeSlots->contains($slot->id)) bg-white text-black @endif  border border-white py-1 px-4 rounded-full">{{ $slot->timeSlot }}</button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-full flex justify-center py-2">
                                <button class="text-black py-3 max-sm:hidden uppercase px-6 font-normal bg-white rounded-lg tracking-tight w-min whitespace-nowrap">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>