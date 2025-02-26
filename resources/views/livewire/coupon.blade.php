<?php

use App\Models\IssuedCoupon;
use App\Models\User;

use function Livewire\Volt\{state, mount, with};

state(['role']);

with(fn() => ['issuedcoupons' => IssuedCoupon::with(['user', 'coupon'])->get()]);

mount(function ($auth) {
    $this->role = $auth->role->name;
});
?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="flex justify-between items-center">
        <div class="text-5xl lg:text-7xl font-avenir-next-bold text-white">Coupons</div>
        @if($role == 'administrator')
        <a href="/manage-coupon" wire:navigate class="text-black py-3 max-sm:hidden uppercase px-6 font-normal bg-white rounded-lg tracking-tight w-min whitespace-nowrap">Manage Coupon</a>
        @endif
    </div>
    <div class="grow relative whitespace-nowrap" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-auto hidden-scrollbar absolute inset-x-0 border border-white rounded-lg" :style="'height: ' + height + 'px;'">
            <table class="w-full overflow-y-hidden backdrop-blur-xl">
                <thead class="sticky top-0 text-black rounded-t-lg">
                    <tr class="bg-white *:p-3">
                        <th class="font-normal">
                            No
                        </th>
                        @if($role == 'administrator')
                        <th class="font-normal">
                            First Name
                        </th>
                        <th class="font-normal">
                            Last Name
                        </th>
                        <th class="font-normal">
                            Phone no
                        </th>
                        @endif
                        <th class="font-normal">
                            Coupon
                        </th>
                        <th class="font-normal">
                            Issued On
                        </th>
                        <th class="font-normal">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issuedcoupons as $issuedcoupon)
                    <tr class="hover:bg-black/10 transition-colors duration-200 text-white *:p-3">
                        <td class="text-center font-normal">{{$loop->iteration}}</td>
                        @if($role == 'administrator')
                        <td class="text-center font-normal">{{$issuedcoupon->user->first_name}}</td>
                        <td class="text-center font-normal">{{$issuedcoupon->user->last_name}}</td>
                        <td class="text-center font-normal">{{$issuedcoupon->user->phoneno}}</td>
                        @endif
                        <td class="text-center font-normal">{{$issuedcoupon->coupon->name}}</td>
                        <td class="text-center font-normal">{{$issuedcoupon->coupon->created_at}}</td>
                        <td class="text-center font-normal">{{$issuedcoupon->is_used ? 'Used' : 'Not Used'}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>