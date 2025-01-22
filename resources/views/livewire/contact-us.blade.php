<?php

use function Livewire\Volt\{state};

//

?>

<div>
    <div class="mt-12 mb-44">
        <div class="uppercase font-avenir-next-rounded-light text-center my-16 text-primary text-3xl">
            Contact Us
        </div>
        <div class="w-3/5 mx-auto my-12 font-avenir-next-rounded-light text-lg text-center text-primary">
            For membership, classes, questions or scheduling / rescheduling, shop inquiries or anything else, please contact us through the form below and we'll reply as soon as we can! Here's a few commonly asked questions too, that you might find helpful.
        </div>
        <form wire:submit="submit" class="w-3/5 mx-auto border py-12">
            <div class="w-4/5 mx-auto grid grid-cols-1 gap-8 font-avenir-next-rounded-light text-primary">
                <div>
                    <label>First Name</label>
                    <input wire:model="first_name" class="w-full bg-black/5 outline-none p-3" placeholder="First Name">
                    <div>
                        @error('first_name')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div>
                    <label>Last Name</label>
                    <input wire:model="last_name" class="w-full bg-black/5 outline-none p-3" placeholder="Last Name">
                    <div>
                        @error('last_name')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div>
                    <label>Email</label>
                    <input wire:model="email" class="w-full bg-black/5 outline-none p-3" placeholder="Email">
                    <div>
                        @error('email')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div>
                    <label>Phone No</label>
                    <input x-mask="9999999999" wire:model="phoneno" type="text" class="w-full bg-black/5 outline-none p-3" placeholder="Phone No">
                    @error('phoneno')
                    <div wire:transition.in.scale.origin.top.duration.1000ms class="text-red-500 text-sm">
                        <span class="error">{{ $message }}</span>
                    </div>
                    @enderror
                </div>
                <div>
                    <label>Message</label>
                    <textarea wire:model="email" class="w-full bg-black/5 outline-none p-3" placeholder="Message"></textarea>
                    <div>
                        @error('email')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="font-avenir-next-rounded-extra-light uppercase text-center py-2 px-4 bg-primary mx-auto text-white text-xl">Submit</button>
            </div>
        </form>
    </div>
</div>