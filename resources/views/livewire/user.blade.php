<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{state, with};

state(['modal' => false, 'selected_user', 'role_id']);

with(fn() => [
    'users' => User::with(['role'])->where('id', '!=', Auth::user()->id)->get(),
    'roles' => Role::get(),
]);

$toggleModal = function ($id = null) {
    $this->modal = !$this->modal;
    if ($id) {
        $this->selected_user = User::with(['role'])->find($id);
        $this->role_id = $this->selected_user->role_id;
    } else {
        $this->reset(['selected_user', 'role_id']);
    }
};

$submit = function () {
    User::find($this->selected_user->id)->update(['role_id' => $this->role_id]);
    $this->toggleModal();
};

?>

<div class="grow flex flex-col gap-4 lg:gap-8 py-4 lg:py-8 text-white w-11/12 mx-auto">
    <div class="flex justify-between items-center">
        <div class="text-5xl lg:text-7xl font-avenir-next-bold text-white">Users</div>
    </div>
    <div class="grow relative whitespace-nowrap" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-auto hidden-scrollbar absolute inset-x-0 border border-white rounded-lg" :style="'height: ' + height + 'px;'">
            <table class="w-full overflow-y-hidden backdrop-blur-xl">
                <thead class="sticky top-0 text-black rounded-t-lg z-10">
                    <tr class="bg-white *:p-3">
                        <th class="font-normal sticky left-0 bg-white">
                            #
                        </th>
                        <th class="font-normal">
                            First Name
                        </th>
                        <th class="font-normal">
                            Last Name
                        </th>
                        <th class="font-normal">
                            Phone no
                        </th>
                        <th class="font-normal">
                            Email
                        </th>
                        <th class="font-normal">
                            Role
                        </th>
                        <th class="font-normal">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="hover:bg-black/10 transition-colors duration-200 text-white *:p-3">
                        <td class="text-center font-normal sticky left-0 bg-white text-black">{{$loop->iteration}}</td>
                        <td class="text-center font-normal">{{$user->first_name}}</td>
                        <td class="text-center font-normal">{{$user->last_name}}</td>
                        <td class="text-center font-normal">{{$user->phoneno}}</td>
                        <td class="text-center font-normal">{{$user->email}}</td>
                        <td class="text-center font-normal capitalize">{{$user->role->name}}</td>
                        <td class="text-center font-normal">
                            <button wire:click="toggleModal({{ $user->id }})">
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

    @if($this->modal)
    <div class="fixed inset-0 flex justify-center items-center">
        <form wire:submit="submit" class="w-1/2 backdrop-blur-3xl shadow-lg border border-white rounded-lg flex flex-col gap-3 p-4">
            <div class="flex justify-end items-center">
                <button type="button" wire:click="toggleModal" class="hover:bg-black/30 rounded-full p-1">
                    <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                    </svg>
                </button>
            </div>
            <div class="border border-white"></div>
            <div class="grid grid-cols-2 gap-4">
                <div class="relative">
                    <input disabled type="text" value="{{ $selected_user->first_name.' '.$selected_user->first_name }}" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-2 border-white appearance-none focus:outline-none focus:ring-0 focus:border-white peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-white peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Name</label>
                </div>
                <div class="relative">
                    <input disabled type="text" value="{{ $selected_user->phoneno }}" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-2 border-white appearance-none focus:outline-none focus:ring-0 focus:border-white peer" placeholder=" " />
                    <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-white peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Phone Number</label>
                </div>
            </div>
            <div class="relative">
                <select wire:model="role_id" type="text" id="floating_outlined" class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent capitalize rounded-lg border-2 border-white appearance-none focus:outline-none focus:ring-0 focus:border-white peer" placeholder=" ">
                    @foreach($roles as $role)
                    <option @if( $role->id == $selected_user->role->id ) selected @endif value={{ $role->id }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                <label for="floating_outlined" class="absolute text-sm rounded-full text-black duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto start-1">Name</label>
            </div>
            <button class="text-black py-3 uppercase px-20 bg-white rounded-lg tracking-tight w-min mx-auto">Submit</button>
        </form>
    </div>
    @endif
</div>