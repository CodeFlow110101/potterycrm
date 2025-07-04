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
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;

usesPagination();

state(['modal' => false, 'selected_item', 'item_status', 'item_id', 'statuses', 'search', 'from' => Carbon::today()->format('d M Y'), 'to' => Carbon::today()->format('d M Y'), 'status_filter' => 'all', 'auth', 'role', 'notify' => true]);

rules(['item_id' => 'required', 'item_status' => 'required'])->attributes(['item_id' => 'item id', 'item_status' => 'status']);

on(['echo:purchase,PurchaseCreated' => function () {
    $this->reset();
}]);

with(fn() => [
    'purchases' => Purchase::with(['items.product' => fn($query) => $query->withTrashed(), 'user'])
        ->where(function ($query) {
            $query->whereHas('user', function ($q) {
                $q->where('email', 'like', '%' . $this->search . '%')
                    ->orWhere('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('phoneno', 'like', '%' . $this->search . '%');
            })->orWhereHas('items', function ($q) {
                $q->where('item_id', 'like', '%' . $this->search . '%');
            })->orWhereHas('items.product', function ($q) {
                $q->withTrashed()->where('name', 'like', '%' . $this->search . '%');
            });
        })
        ->when(Gate::allows('view-purchase-filters'), fn($query) => $query->whereBetween('created_at', [Carbon::createFromFormat('d M Y', $this->from)->toDateString(), Carbon::createFromFormat('d M Y', $this->to)->toDateString()]))
        ->when($this->status_filter != 'all', function ($query) {
            $query->whereHas('items.status', function ($query) {
                $query->where('name', $this->status_filter);
            });
        })
        ->when(!Gate::allows('view-any-order'), function ($query) {
            $query->where('user_id', Auth::user()->id);
        })
        ->latest()
        ->get(),
    'status_filter_options' => PurchaseItemStatus::get()
]);

$toggleModal = function ($id = null) {
    $this->modal = !$this->modal;
    if ($id) {
        $this->selected_item = PurchaseItem::with(['purchase.user', 'product' => function ($query) {
            $query->withTrashed();
        }, 'status'])->find($id);
        $this->item_id = $this->selected_item->item_id ? $this->selected_item->item_id : '';
        $this->item_status = $this->selected_item->status_id;
        $this->statuses = PurchaseItemStatus::get();
        $this->notify = true;
    } else {
        $this->reset(['selected_item', 'item_id', 'item_status', 'statuses']);
        $this->notify = true;
        $this->resetValidation();
    }
};

$submit = function () {
    $this->validate();

    $this->notify && $this->selected_item->update(['status_id' => $this->item_status, 'item_id' => $this->item_id]);
    $this->notify ||  $this->selected_item->updateQuietly(['status_id' => $this->item_status, 'item_id' => $this->item_id]);
    $this->selected_item->refresh();
    $this->toggleModal();
};



mount(function ($auth) {
    $this->auth = $auth;
    $this->role = $this->auth->role->name;
});
?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="text-5xl lg:text-7xl font-avenir-next-bold text-white">Orders</div>
    @can('view-purchase-filters')
    <div class="flex items-center gap-4 w-full max-sm:flex-col *:w-full">
        <div>
            <div class="flex gap-3 items-center px-2.5 py-2.5 w-full text-sm text-white font-semibold backdrop-blur-2xl bg-black/10 rounded-lg border-2 border-white">
                <div>
                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input wire:model.live="search" type="text" value="" id="floating_outlined" class="block size-full bg-transparent appearance-none focus:outline-none focus:ring-0 peer placeholder:text-white/70" placeholder="Search" />
            </div>
        </div>
        <div class="flex items-center gap-4 max-sm:flex-col">
            <div class="max-sm:w-full">
                <div x-data="{ show: false }" @click="show=!show;" @click.away="show=false" class="relative cursor-pointer">
                    <input readonly wire:model.live="status_filter" type="text" value="" id="floating_outlined" class="block cursor-pointer capitalize px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-black/10 backdrop-blur-2xl rounded-lg border-2 outline-none border-white appearance-none peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Status</label>
                    <div x-cloak x-show="show" class="absolute z-50 top-14 block w-full text-sm text-white overflow-clip bg-black/10 backdrop-blur-2xl rounded-lg border-2 outline-none border-white appearance-none peer">
                        <button wire:click="$set('status_filter','all')" class="py-2.5 px-2.5 capitalize hover:bg-black/20 w-full text-start">all</button>
                        @foreach($status_filter_options as $option)
                        <button wire:click="$set('status_filter','{{ $option->name }}')" class="py-2.5 px-2.5 capitalize hover:bg-black/20 w-full text-start">{{ $option->name }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4 w-full *:w-full">
                <div x-data="flatpickrDate(null,null)">
                    <div class="relative cursor-pointer">
                        <input readonly x-ref="dateInput" wire:model.live="from" type="text" value="" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-black/10 backdrop-blur-2xl rounded-lg border-2 outline-none border-white appearance-none peer" placeholder=" " />
                        <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">From</label>
                    </div>
                </div>
                <div x-data="flatpickrDate(null,null)">
                    <div class="relative cursor-pointer">
                        <input readonly x-ref="dateInput" wire:model.live="to" type="text" value="" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-black/10 backdrop-blur-2xl rounded-lg border-2 outline-none border-white appearance-none peer" placeholder=" " />
                        <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">To</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    <div class="grow flex flex-col w-full whitespace-nowrap">
        <div class="font-medium text-black/60 h-full flex flex-col grow">
            <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
                <div class="overflow-auto hidden-scrollbar absolute inset-x-0 border border-white rounded-lg" :style="'height: ' + height + 'px;'">
                    <table class="w-full overflow-y-hidden backdrop-blur-xl">
                        <thead class="bg-white text-black sticky top-0 z-10">
                            <tr class="bg-white *:p-3">
                                <th class="font-normal sticky left-0 bg-white">
                                    #
                                </th>
                                @can('view-customer-detail-columns-order')
                                <th class="font-normal">
                                    Customer Name
                                </th>
                                <th class="font-normal">
                                    Phone No
                                </th>
                                <th class="font-normal">
                                    Email
                                </th>
                                @endcan
                                <th class="font-normal">
                                    Name
                                </th>
                                <th class="font-normal">
                                    Price
                                </th>
                                <th class="font-normal">
                                    Item Id
                                </th>
                                <th class="font-normal">
                                    Item Status
                                </th>
                                @can('view-customer-detail-columns-order')
                                <th class="font-normal max-sm:hidden">
                                    Status
                                </th>
                                @endcan
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
                        <tr class="hover:bg-black/10 transition-colors duration-200 text-white *:p-3">
                            <td class="text-center font-normal sticky left-0 bg-white text-black">{{$iteration}}</td>
                            @can('view-customer-detail-columns-order')
                            <td class="text-center font-normal">{{$purchase->user->first_name . ' ' . $purchase->user->last_name}}</td>
                            <td class="text-center font-normal">{{$purchase->user->phoneno}}</td>
                            <td class="text-center font-normal">{{$purchase->user->email}}</td>
                            @endcan
                            <td class="text-center font-normal">{{$item->product->name}}</td>
                            <td class="text-center font-normal">$ {{number_format($item->product->price / 100, 2, '.', '')}}</td>
                            <td class="text-center font-normal">{{$item->item_id}}</td>
                            <td class="text-center font-normal capitalize">{{$item->status ? $item->status->name : ''}}</td>
                            @can('view-customer-detail-columns-order')
                            <td class="text-center font-normal flex justify-center items-center gap-2 max-sm:hidden">
                                <button wire:click="toggleModal({{$item->id}})">
                                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z" clip-rule="evenodd" />
                                        <path fill-rule="evenodd" d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </td>
                            @endcan
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
            <div class="flex justify-center items-center gap-6">
                <button type="submit" wire:loading.class="pointer-events-none" wire:dirty.class.remove="pointer-events-none opacity-50" class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight relative">
                    <div wire:loading.class="invisible" wire:target="submit">Submit</div>
                    <div wire:loading.class.remove="invisible" wire:target="submit" class="absolute inset-0 flex justify-center items-center invisible">
                        <svg aria-hidden="true" class="w-8 h-8 text-transparent animate-spin fill-white" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                        </svg>
                    </div>
                </button>
                <div class="flex justify-center items-center gap-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="notify" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ms-3 text-sm font-medium text-white">Notify Customer</span>
                    </label>
                </div>
            </div>
        </form>
    </div>
    @endif
</div>