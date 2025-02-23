<?php

use function Livewire\Volt\{state, mount, with, on, usesPagination, rules};

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\SmsController;
use App\Models\PurchaseItemStatus;
use Livewire\Attributes\Validate;

usesPagination();

state(['modal' => false, 'selected_item', 'item_status', 'item_id', 'statuses', 'auth', 'role']);

rules(['item_id' => 'required', 'item_status' => 'required'])->attributes(['item_id' => 'item id', 'item_status' => 'status']);

on(['echo:order,.admin' => function ($request) {
    $this->resetPage();
}]);

with(fn() => [
    'purchases' => Purchase::with(['items.product'])
        ->when($this->role !== 'administrator', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })
        ->get()
]);

$toggleModal = function ($id = null) {
    $this->modal = !$this->modal;
    if ($id) {
        $this->selected_item = PurchaseItem::with(['purchase.user', 'product', 'status'])->find($id);
        $this->item_id = $this->selected_item->item_id ? $this->selected_item->item_id : '';
        $this->item_status = $this->selected_item->status_id;
        $this->statuses = PurchaseItemStatus::get();
    } else {
        $this->reset(['selected_item', 'item_id', 'item_status', 'statuses']);
        $this->resetValidation();
    }
};

$submit = function () {
    $this->validate();
    $this->selected_item->update(['status_id' => $this->item_status, 'item_id' => $this->item_id]);
    $this->selected_item->refresh();
    // App::call([SmsController::class, 'send'], ['phoneno' => env('TWILIO_PHONE_COUNTRY_CODE') . $this->selected_item->purchase->user->phoneno, 'message' => 'Your purchased item with item id ' . $this->selected_item->item_id . ' is ' . $this->selected_item->status->name . '.']);
    $this->toggleModal();
};



mount(function ($auth) {
    $this->auth = $auth;
    $this->role = $this->auth->role->name;
});
?>

<div class="grow flex flex-col gap-8 py-8 text-white w-11/12 mx-auto">
    <div class="text-7xl font-avenir-next-bold text-white">Orders</div>
    <div class="grow flex flex-col w-full">
        <div class="font-medium text-black/60 h-full flex flex-col grow">
            <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
                <div class="overflow-y-auto absolute inset-x-0 border border-white rounded-lg" :style="'height: ' + height + 'px;'">
                    <table class="w-full overflow-y-hidden backdrop-blur-xl">
                        <thead class="bg-white text-black sticky top-0">
                            <tr class="bg-white">
                                <th class="font-normal py-2">
                                    #
                                </th>
                                <th class="font-normal py-2">
                                    Name
                                </th>
                                <th class="font-normal py-2">
                                    Price
                                </th>
                                <th class="font-normal py-2">
                                    Item Id
                                </th>
                                <th class="font-normal py-2">
                                    Item Status
                                </th>
                                @if($this->auth->role->name == 'administrator')
                                <th class="font-normal py-2">
                                    Status
                                </th>
                                @endif
                            </tr>
                        </thead>
                        @php
                        $iteration = 0;
                        @endphp
                        @foreach($purchases as $purchase)
                        @foreach($purchase->items as $item)
                        @php
                        $iteration++;
                        @endphp
                        <tr class="hover:bg-black/10 transition-colors duration-200 text-white">
                            <td class="text-center font-normal py-3">{{$iteration}}</td>
                            <td class="text-center font-normal py-3">{{$item->product->name}}</td>
                            <td class="text-center font-normal py-3">$ {{number_format($item->product->price, 2, '.', '')}}</td>
                            <td class="text-center font-normal py-3">{{$item->item_id}}</td>
                            <td class="text-center font-normal py-3 capitalize">{{$item->status ? $item->status->name : ''}}</td>
                            @if($this->auth->role->name == 'administrator')
                            <td class="text-center font-normal py-3 flex justify-center items-center gap-2">
                                <button wire:click="toggleModal({{$item->id}})">
                                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z" clip-rule="evenodd" />
                                        <path fill-rule="evenodd" d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if($modal)
    <div class="fixed inset-0 flex flex-col">
        <form wire:submit="submit" class="m-auto border w-3/4 backdrop-blur-3xl border-white shadow-xl rounded-lg p-4 flex flex-col gap-4">
            <div class="flex justify-end">
                <div wire:click="toggleModal" class="rounded-full hover:bg-black/30 p-0.5 group">
                    <svg class="size-6 text-white " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                </div>
            </div>
            <div class="border border-white"></div>
            <div class="grow">
                <div class="border border-white rounded-lg flex items-center justify-center p-2 py-24 h-full">
                    <div class="flex justify-stretch items-center w-11/12">
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.item_status = (1).toString();" :class="[1 , 2 , 3 , 4].includes(Number($wire.item_status)) ? 'bg-white text-black border-white' : 'hover:bg-white hover:text-black hover:border-white transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">1</button>
                            <div :class="[1 , 2 , 3 , 4].includes(Number($wire.item_status)) ? 'text-white' : 'text-black/30'" class="absolute pointer-events-none -translate-y-full pb-2 font-semibold text-center"><span class="whitespace-nowrap">Ready for</span> Firing</div>
                        </div>
                        <div class="w-full h-1.5 bg-black/30 relative overflow-hidden">
                            <div class="absolute inset-0">
                                <div :class="[2 , 3 , 4].includes(Number($wire.item_status))  ? 'w-full' : 'w-0'" class="h-full bg-white transition-all duration-200"></div>
                            </div>
                        </div>
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.item_status = (2).toString();" :class="[2 , 3 , 4].includes(Number($wire.item_status)) ? 'bg-white text-black border-white' : 'hover:bg-white hover:text-black hover:border-white transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">2</button>
                            <div :class="[2 , 3 , 4].includes(Number($wire.item_status)) ? 'text-white' : 'text-black/30'" class="absolute pointer-events-none translate-y-full pt-2 font-semibold text-center"><span class="whitespace-nowrap">Firing in</span> Progress</div>
                        </div>
                        <div class="w-full h-1.5 bg-black/30 relative overflow-hidden">
                            <div class="absolute inset-0">
                                <div :class="[3 , 4].includes(Number($wire.item_status))  ? 'w-full' : 'w-0'" class="h-full bg-white transition-all duration-200"></div>
                            </div>
                        </div>
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.item_status = (3).toString()" :class="[3 , 4].includes(Number($wire.item_status)) ? 'bg-white text-black border-white' : 'hover:bg-white hover:text-black hover:border-white transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">3</button>
                            <div :class="[3 , 4].includes(Number($wire.item_status)) ? 'text-white' : 'text-black/30'" class="absolute pointer-events-none -translate-y-full pb-2 font-semibold text-center"><span class="whitespace-nowrap"> Firing Process</span> Finished</div>
                        </div>
                        <div class="w-full h-1.5 bg-black/30 relative overflow-hidden">
                            <div class="absolute inset-0">
                                <div :class="[4].includes(Number($wire.item_status))  ? 'w-full' : 'w-0'" class="h-full bg-white transition-all duration-200"></div>
                            </div>
                        </div>
                        <div class="size-8 shrink-0 flex justify-center items-center relative">
                            <button type="button" @click="$wire.item_status = (4).toString()" :class="[4].includes(Number($wire.item_status)) ? 'bg-white text-black border-white' : 'hover:bg-white hover:text-black hover:border-white transition-colors duration-200 border-2 border-black/30'" class="rounded-full size-full flex justify-center items-center">4</button>
                            <div :class="[4].includes(Number($wire.item_status)) ? 'text-white' : 'text-black/30'" class="absolute pointer-events-none translate-y-full pt-2 font-semibold text-center"><span class="whitespace-nowrap">Ready for</span> Pickup</div>
                        </div>
                    </div>
                </div>
                @error('item_status')
                <div class="text-red-white text-sm">{{$message}}</div>
                @enderror
            </div>
            <div class="grid grid-cols-4">
                <div class="flex justify-center items-center">Name: {{$selected_item->purchase->user->first_name}} {{$selected_item->purchase->user->first_name}}</div>
                <div class="flex justify-center items-center">Item: {{$selected_item->product->name}}</div>
                <div class="flex justify-center gap-2 items-center">
                    <div>Item Id:</div>
                    <div class="flex flex-col gap-1">
                        <input wire:model="item_id" class="bg-transparent border border-white rounded-md outline-none p-1 h-min">
                        @error('item_id')
                        <div class="text-red-white text-sm">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-center items-center capitalize">Status: {{$selected_item->status ? $selected_item->status->name : ''}}</div>
            </div>
            <div class="flex justify-center items-center">
                <button type="submit" wire:loading.class="pointer-events-none" wire:dirty.class.remove="pointer-events-none opacity-50" class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight relative">
                    <div wire:loading.class="invisible" wire:target="submit">Submit</div>
                    <div wire:loading.class.remove="invisible" wire:target="submit" class="absolute inset-0 flex justify-center items-center invisible">
                        <svg aria-hidden="true" class="w-8 h-8 text-transparent animate-spin fill-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                        </svg>
                    </div>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>