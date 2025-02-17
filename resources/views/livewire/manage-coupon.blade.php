<?php

use App\Models\Coupon;

use function Livewire\Volt\{state, rules, with};

state(['modal' => false, 'name', 'repeat_cycle', 'repeat', 'discount_percentage', 'validity', 'status', 'id', 'min_cart_value']);

rules(['name' => 'required', 'repeat_cycle' => 'required', 'repeat' => 'required', 'discount_percentage' => 'required', 'validity' => 'required', 'status' => 'required', 'min_cart_value' => 'required']);

with(fn() => ['coupons' => Coupon::get()]);

$submit = function () {
    $this->validate();

    if ($this->id) {
        Coupon::find($this->id)->update([
            'name' => $this->name,
            'repeat_cycle' => $this->repeat_cycle,
            'repeat' => $this->repeat,
            'discount_type' => 'percentage',
            'discount_value' => $this->discount_percentage,
            'validity' => $this->validity,
            'status' => $this->status,
            'min_cart_value' => $this->min_cart_value,
        ]);
    } else {
        Coupon::create([
            'name' => $this->name,
            'repeat_cycle' => $this->repeat_cycle,
            'repeat' => $this->repeat,
            'discount_type' => 'percentage',
            'discount_value' => $this->discount_percentage,
            'validity' => $this->validity,
            'status' => $this->status,
            'min_cart_value' => $this->min_cart_value,
        ]);
    }

    $this->toggleModal();
};

$toggleModal = function ($id = null) {
    $this->id = $id;
    if ($id) {
        $coupon = Coupon::find($id);

        $this->name = $coupon->name;
        $this->repeat_cycle = $coupon->repeat_cycle;
        $this->repeat = $coupon->repeat;
        $this->discount_percentage = $coupon->discount_value;
        $this->validity = $coupon->validity;
        $this->status = $coupon->status;
        $this->min_cart_value = $coupon->min_cart_value;
    }

    $this->modal = !$this->modal;

    if (!$this->modal) {
        $this->reset(['name', 'repeat_cycle', 'repeat', 'discount_percentage', 'validity', 'status', 'id']);
    }
};
?>

<div class="grow flex flex-col gap-8 py-8 text-white w-11/12 mx-auto">
    <div class="flex justify-between items-center">
        <div class="text-7xl font-avenir-next-bold text-white">Manage Coupons</div>
        <button wire:click="toggleModal" class="text-black py-3 uppercase px-6 font-normal bg-white rounded-lg tracking-tight w-min whitespace-nowrap">Add Coupon</button>
    </div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-y-auto absolute inset-x-0 border border-white rounded-lg" :style="'height: ' + height + 'px;'">
            <table class="w-full overflow-y-hidden backdrop-blur-xl">
                <thead class="bg-white text-black sticky top-0">
                    <tr class="bg-white">
                        <th class="font-normal py-2">
                            No
                        </th>
                        <th class="font-normal py-2">
                            Name
                        </th>
                        <th class="font-normal py-2">
                            Repeat Cycle in Days
                        </th>
                        <th class="font-normal py-2">
                            Repeat
                        </th>
                        <th class="font-normal py-2">
                            Min Cart Value
                        </th>
                        <th class="font-normal py-2">
                            Validity
                        </th>
                        <th class="font-normal py-2">
                            Status
                        </th>
                        <th class="font-normal py-2">
                            Acton
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coupons as $coupon)
                    <tr class="hover:bg-black/10 transition-colors duration-200 text-white">
                        <td class="text-center font-normal py-3">{{ $loop->iteration }}</td>
                        <td class="text-center font-normal py-3">{{ $coupon->name }}</td>
                        <td class="text-center font-normal py-3">{{ $coupon->repeat_cycle }}</td>
                        <td class="text-center font-normal py-3">{{ $coupon->repeat ? 'Yes' : 'No' }}</td>
                        <td class="text-center font-normal py-3">${{ $coupon->min_cart_value }}</td>
                        <td class="text-center font-normal py-3">{{ $coupon->validity }}</td>
                        <td class="text-center font-normal py-3">{{ $coupon->status ? 'Active' : 'Inactive' }}</td>
                        <td class="text-center font-normal py-3 capitalize flex justify-center">
                            <button wire:click="toggleModal({{ $coupon->id }})">
                                <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
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
    <div class="fixed inset-0 flex justify-center items-center text-white text-base">
        <div class="m-auto py-4 w-1/2 shadow flex flex-col gap-2 border border-white rounded-lg backdrop-blur-3xl">
            <div class="flex justify-end w-11/12 mx-auto">
                <button wire:click="toggleModal" class="outline-none rounded-full hover:bg-black/30 p-1 group">
                    <svg class="size-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                </button>
            </div>
            <div class="border w-11/12 mx-auto"></div>
            <form wire:submit="submit" class="w-11/12 mx-auto py-4 flex flex-col gap-4">
                <div>
                    <label class="font-avenir-next-rounded-semibold text-xl">Name</label>
                    <input wire:model="name" class="w-full bg-black/5 outline-none p-3" placeholder="Last Name">
                    <div>
                        @error('name')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex gap-8">
                    <div class="flex-1">
                        <label class="font-avenir-next-rounded-semibold text-xl">Repeat Cycle in Days</label>
                        <input wire:model="repeat_cycle" x-mask="99" class="w-full bg-black/5 outline-none p-3" placeholder="Repeat Cycle in Days">
                        <div>
                            @error('repeat_cycle')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex-1">
                        <label class="font-avenir-next-rounded-semibold text-xl">Minimum Cart Value</label>
                        <input wire:model="min_cart_value" x-mask="99999999" class="w-full bg-black/5 outline-none p-3" placeholder="Repeat Cycle in Days">
                        <div>
                            @error('min_cart_value')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="flex gap-8">
                    <div class="flex-1">
                        <label class="font-avenir-next-rounded-semibold text-xl">Repeat</label>
                        <select wire:model="repeat" class="w-full bg-black/5 outline-none p-3">
                            <option value="">Select a Repeat Status</option>
                            <option value="true">Yes</option>
                            <option value="false">No</option>
                        </select>
                        <div>
                            @error('repeat')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex-1">
                        <label class="font-avenir-next-rounded-semibold text-xl">Discount Percentage</label>
                        <input wire:model="discount_percentage" x-mask="99" class="w-full bg-black/5 outline-none p-3" placeholder="Discount Percentage">
                        <div>
                            @error('discount_percentage')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="flex gap-8">
                    <div class="flex-1">
                        <label class="font-avenir-next-rounded-semibold text-xl">Validity in days</label>
                        <input wire:model="validity" x-mask="99" class="w-full bg-black/5 outline-none p-3" placeholder="Discount Percentage">
                        <div>
                            @error('validity')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex-1">
                        <label class="font-avenir-next-rounded-semibold text-xl">Status</label>
                        <select wire:model="status" class="w-full bg-black/5 outline-none p-3">
                            <option value="">Select a Status</option>
                            <option value="true">Active</option>
                            <option value="false">Inactive</option>
                        </select>
                        <div>
                            @error('status')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <button class="text-black py-3 uppercase px-6 font-normal bg-white rounded-lg tracking-tight w-min whitespace-nowrap mx-auto">Submit</button>
            </form>
        </div>
    </div>
    @endif
</div>