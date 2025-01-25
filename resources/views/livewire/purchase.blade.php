<?php

use function Livewire\Volt\{state, mount, with, on};

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Carbon;

state(['auth']);

on(['echo:purchase,.admin' => function ($request) {
    $this->reset();
}]);

with(fn() => ['purchases' => Purchase::with(['payment'])
    ->when($this->auth->role->name !== 'administrator', function ($query) {
        $query->where('user_id', $this->auth->id);
    })
    ->get()]);

mount(function ($auth) {
    $this->auth = $auth;
});
?>

<div class="grow flex flex-col text-primary font-avenir-next-rounded-light">
    <div class="grow flex flex-col w-full">
        <div class="font-medium flex flex-col grow text-black/60 h-full">
            <div class="py-4 flex justify-end items-center">
                <div class="relative">
                    <input class="border outline-none py-2 pl-10 pr-4">
                    <div class="absolute inset-y-0 flex items-center pl-2">
                        <svg class="w-6 h-6 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
                <div class="overflow-y-auto absolute inset-x-0" :style="'height: ' + height + 'px;'">
                    <table class="w-full overflow-y-hidden">
                        <thead class="bg-white sticky top-0">
                            <tr class="bg-primary/40">
                                <th class="font-medium py-2">
                                    #
                                </th>
                                <th class="font-medium py-2">
                                    Amount
                                </th>
                                <th class="font-medium py-2">
                                    Source
                                </th>
                                <th class="font-medium py-2">
                                    Type
                                </th>
                                <th class="font-medium py-2">
                                    Completed
                                </th>
                                <th class="font-medium py-2">
                                    Date
                                </th>
                                <th class="font-medium py-2">
                                    Reciept
                                </th>
                            </tr>
                        </thead>
                        @foreach($purchases as $purchase)
                        <tr class="hover:bg-black/10 transition-colors duration-200 text-primary">
                            <td class="text-center font-normal py-1">{{$loop->iteration}}</td>
                            <td class="text-center font-normal py-1">$ {{number_format($purchase->payment->amount/100, 2, '.', '')}}</td>
                            <td class="text-center font-normal py-1">{{$purchase->payment->source}}</td>
                            <td class="text-center font-normal py-1">{{$purchase->payment->type}}</td>
                            <td class="text-center font-normal py-1">{{$purchase->payment->status}}</td>
                            <td class="text-center font-normal py-1">{{ Carbon::parse($purchase->created_at)->format('j M Y') }}</td>
                            <td class="text-center font-normal py-1 flex justify-center items-center">
                                <a href="{{$purchase->payment->receipt_url}}" class="text-center font-normal py-3" target="_blank" rel="noopener noreferrer">
                                    <svg class="w-6 h-6 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M5.617 2.076a1 1 0 0 1 1.09.217L8 3.586l1.293-1.293a1 1 0 0 1 1.414 0L12 3.586l1.293-1.293a1 1 0 0 1 1.414 0L16 3.586l1.293-1.293A1 1 0 0 1 19 3v18a1 1 0 0 1-1.707.707L16 20.414l-1.293 1.293a1 1 0 0 1-1.414 0L12 20.414l-1.293 1.293a1 1 0 0 1-1.414 0L8 20.414l-1.293 1.293A1 1 0 0 1 5 21V3a1 1 0 0 1 .617-.924ZM9 7a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2H9Zm0 4a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Zm0 4a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>