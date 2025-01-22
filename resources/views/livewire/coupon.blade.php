<?php

use App\Models\IssuedCoupon;

use function Livewire\Volt\{state, mount, with};

state(['role']);

with(fn() => ['issuedcoupons' => IssuedCoupon::with(['user', 'coupon'])->get()]);

mount(function ($auth) {
    $this->role = $auth->role->name;
});
?>

<div class="font-avenir-next-rounded-light">
    @if($role == 'administrator')
    <div class="flex justify-end py-4">
        <a href="/manage-coupon" wire:navigate class="bg-primary bg-opacity-90 hover:bg-opacity-100 text-white py-3 px-6 uppercase font-avenir-next-rounded-extra-light tracking-wider">Manage Coupon</a>
    </div>
    @endif
    <div class="h-[70vh]">
        <table class="w-full overflow-y-hidden">
            <thead class="bg-primary/40">
                <tr>
                    <th class="font-normal py-2">
                        No
                    </th>
                    @if($role == 'administrator')
                    <th class="font-normal py-2">
                        First Name
                    </th>
                    <th class="font-normal py-2">
                        Last Name
                    </th>
                    <th class="font-normal py-2">
                        Phone no
                    </th>
                    @endif
                    <th class="font-normal py-2">
                        Coupon
                    </th>
                    <th class="font-normal py-2">
                        Issued On
                    </th>
                    <th class="font-normal py-2">
                        Status
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($issuedcoupons as $issuedcoupon)
                <tr class="hover:bg-black/10 transition-colors duration-200 text-primary">
                    <td class="text-center font-normal py-3">{{$loop->iteration}}</td>
                    @if($role == 'administrator')
                    <td class="text-center font-normal py-3">{{$issuedcoupon->user->first_name}}</td>
                    <td class="text-center font-normal py-3">{{$issuedcoupon->user->last_name}}</td>
                    <td class="text-center font-normal py-3">{{$issuedcoupon->user->phoneno}}</td>
                    @endif
                    <td class="text-center font-normal py-3">{{$issuedcoupon->coupon->name}}</td>
                    <td class="text-center font-normal py-3">{{$issuedcoupon->coupon->created_at}}</td>
                    <td class="text-center font-normal py-3">{{$issuedcoupon->is_used ? 'Used' : 'Not Used'}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>