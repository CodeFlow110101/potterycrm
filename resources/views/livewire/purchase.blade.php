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

<div class="grow flex flex-col gap-8 py-8 text-white w-11/12 mx-auto">
    <div class="text-7xl font-avenir-next-bold text-white">Purchases</div>
    <div class="grow flex flex-col w-full">
        <div class="font-medium flex flex-col grow text-black h-full">
            <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
                <div class="overflow-y-auto absolute inset-x-0 border border-white rounded-lg" :style="'height: ' + height + 'px;'">
                    <table class="w-full overflow-y-hidden backdrop-blur-xl">
                        <thead class="bg-white sticky top-0">
                            <tr class="bg-white text-black">
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
                        <tr class="hover:bg-black/10 transition-colors duration-200 text-white">
                            <td class="text-center font-normal py-1">{{$loop->iteration}}</td>
                            <td class="text-center font-normal py-1">$ {{number_format($purchase->payment->amount/100, 2, '.', '')}}</td>
                            <td class="text-center font-normal py-1">{{$purchase->payment->source}}</td>
                            <td class="text-center font-normal py-1">{{$purchase->payment->type}}</td>
                            <td class="text-center font-normal py-1">{{$purchase->payment->status}}</td>
                            <td class="text-center font-normal py-1">{{ Carbon::parse($purchase->created_at)->format('j M Y') }}</td>
                            <td class="text-center font-normal py-1 flex justify-center items-center">
                                <a href="{{$purchase->payment->receipt_url}}" class="text-center font-normal py-3" target="_blank" rel="noopener noreferrer">
                                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
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