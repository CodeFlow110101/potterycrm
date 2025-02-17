<?php

use function Livewire\Volt\{state};

//

?>

<div class="grow flex flex-col gap-8 py-8 text-white w-11/12 mx-auto">
    <div class="text-7xl font-avenir-next-bold">Contact Us</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-y-auto hidden-scrollbar absolute inset-x-0" :style="'height: ' + height + 'px;'">
            <form wire:submit="submit" class="w-3/5 mx-auto py-12 backdrop-blur-xl border border-white rounded-lg">
                <div class="w-4/5 mx-auto grid grid-cols-1 gap-8 font-avenir-next-rounded-light">
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">First Name</label>
                        <input wire:model="first_name" class="w-full bg-black/5 outline-none p-3" placeholder="First Name">
                        <div>
                            @error('first_name')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Last Name</label>
                        <input wire:model="last_name" class="w-full bg-black/5 outline-none p-3" placeholder="Last Name">
                        <div>
                            @error('last_name')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Email</label>
                        <input wire:model="email" class="w-full bg-black/5 outline-none p-3" placeholder="Email">
                        <div>
                            @error('email')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Phone No</label>
                        <input x-mask="9999999999" wire:model="phoneno" type="text" class="w-full bg-black/5 outline-none p-3" placeholder="Phone No">
                        @error('phoneno')
                        <div wire:transition.in.scale.origin.top.duration.1000ms>
                            <span class="error">{{ $message }}</span>
                        </div>
                        @enderror
                    </div>
                    <div>
                        <label class="font-avenir-next-rounded-semibold text-xl">Message</label>
                        <textarea wire:model="email" class="w-full bg-black/5 outline-none p-3" placeholder="Message"></textarea>
                        <div>
                            @error('email')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="text-black py-3 uppercase px-6 mx-auto bg-white rounded-lg tracking-tight">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>