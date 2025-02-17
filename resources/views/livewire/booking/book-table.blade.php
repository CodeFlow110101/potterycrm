<?php

use App\Http\Controllers\SmsController;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use function Livewire\Volt\{state, rules, with};

state(['first_name', 'last_name', 'email', 'phoneno', 'otp', 'generatedOtp', 'noofpeople', 'booking_date_time', 'date']);

rules(['first_name' => 'required', 'last_name' => 'required', 'email' => 'required|email', 'phoneno' => 'required', 'booking_date_time' => "required"]);

with(fn() => ['slots' => []]);


$verifyOtp = function () {

    $this->validate();
    $this->resetValidation();

    if (App::call([SmsController::class, 'verifyOtp'], ['id' => $this->generatedOtp->id, 'userOtp' => $this->otp])) {

        if (User::where('phoneno', $this->phoneno)->exists()) {
            User::where('phoneno', $this->phoneno)->first()->bookings()->create([
                'status_id' => 1,
                'no_of_people' => $this->noofpeople,
                'booking_datetime' => Carbon::createFromFormat('h:i A | d-m-Y', $this->booking_date_time)->format('Y-m-d H:i:s'),
            ]);
        } else {
            $user = User::Create(
                [
                    'email' => $this->email,
                    'phoneno' => $this->phoneno,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'role_id' => 2,
                    'password' => Hash::make('12345678'),
                ]
            );

            $user->bookings()->create([
                'status_id' => 1,
                'no_of_people' => $this->noofpeople,
                'booking_datetime' => Carbon::createFromFormat('h:i A | d-m-Y', $this->booking_date_time)->format('Y-m-d H:i:s'),
            ]);
        }
        $this->dispatch('show-toastr', type: 'success', message: 'Your booking has been successfully registered');
        App::call([SmsController::class, 'send'], ['phoneno' => env('TWILIO_PHONE_COUNTRY_CODE') . $this->phoneno, 'message' => 'Your otp is ' . $this->generatedOtp->otp . '.']);
        $this->reset();
        $this->dispatch('reset');
    } else {
        $this->addError('otp', 'Confirmation Code is invalid');
    }
};

$submit = function () {
    $this->validate();

    if (User::where('phoneno', $this->phoneno)->exists() && User::where('phoneno', $this->phoneno)->first()->email != $this->email) {
        $this->addError('phoneno', 'This phone no is already taken with another email.');
        return;
    } elseif (User::where('email', $this->email)->exists() && User::where('email', $this->email)->first()->phoneno != $this->phoneno) {
        $this->addError('email', 'This email is already taken with another phone no.');
        return;
    }

    $this->generatedOtp = App::call([SmsController::class, 'generateOtp']);
    App::call([SmsController::class, 'send'], ['phoneno' => env('TWILIO_PHONE_COUNTRY_CODE') . $this->phoneno, 'message' => 'Your otp is ' . $this->generatedOtp->otp . '.']);
    $this->dispatch('show-toastr', type: 'success', message: 'A code has been sent to this number');
    $this->dispatch('start-countdown');
};

?>

<div class="grow flex flex-col gap-8 py-8 text-white w-11/12 mx-auto">
    <div class="text-7xl font-avenir-next-bold text-white">Book a Table</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="overflow-y-auto hidden-scrollbar absolute inset-x-0 flex flex-col grow" :style="'height: ' + height + 'px;'">
            <form x-data="otp" x-on:reset="reset()" x-on:start-countdown="startCountdown()" wire:submit="submit" class="backdrop-blur-xl border border-white rounded-lg p-4 grow">
                <div class="grow flex gap-12">
                    <div class="flex-1">
                        <input x-data="flatpickrDate(null)" type="text" x-ref="dateInput" wire:model.live="date" class="hidden" placeholder="Select a date">
                        <div class=" w-full flex justify-end">
                            <div class="border-y border-l w-full rounded-l-lg flex">
                                <div class="mx-auto w-4/5 h-4/5 my-auto">
                                    <div class="text-2xl" x-text="year('{{$date}}')"></div>
                                    <div x-text="date('{{$date}}')"></div>
                                </div>
                            </div>
                            <div wire:ignore x-ref="calendarContainer" class="flex justify-center"></div>
                        </div>
                        @error('date')
                        <div wire:transition.in.scale.origin.top.duration.1000ms class="text-red-500 text-sm">
                            <span class="error">{{ $message }}</span>
                        </div>
                        @enderror
                    </div>
                    <div class="flex-1 flex flex-col gap-4">
                        <div class="flex flex-col gap-4 bg-white text-black p-4 rounded-lg">
                            <div class="text-2xl" x-text="'No of people'"></div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <input wire:model="people" x-mask="99" @input="if ($event.target.value.trim() === '' || $event.target.value.trim() === '00' || $event.target.value.trim() === '0') $event.target.value = 1" class="bg-transparent text-xl text-center outline-none border border-black p-4 w-24" x-mask="99" />
                                </div>
                            </div>
                            @error('people')
                            <div wire:transition.in.scale.origin.top.duration.1000ms class="text-red-500 text-sm">
                                <span class="error">{{ $message }}</span>
                            </div>
                            @enderror
                        </div>
                        <div class="flex flex-col border p-4 bg-white gap-4 text-black rounded-lg">
                            <div class="text-2xl" x-text="'Time'"></div>
                            <div>
                                <div class="flex flex-wrap justify-around gap-4 text-sm">
                                    @foreach($slots as $slot)
                                    <button wire:click="toggleTimeSlot('{{$slot}}')" class="border border-black py-1 px-4 rounded-full @if(in_array($slot,$selectedTimeSlots)) bg-black text-white @endif" x-text="timeSlot('{{$slot}}')"></button>
                                    @endforeach
                                </div>
                            </div>
                            @error('selectedTimeSlots')
                            <div wire:transition.in.scale.origin.top.duration.1000ms class="text-red-500 text-sm">
                                <span class="error">{{ $message }}</span>
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>





                <div x-show="false" class="h-min grid grid-cols-1 gap-8 w-4/5 mx-auto font-avenir-next-rounded-light text-primary">
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
                    <div x-data x-init="flatpickr($refs.input, { dateFormat: 'H:i K d-m-Y', enableTime: true , time_24hr: false, })" class="w-full">
                        <label>Booking Date & Time</label>
                        <input wire:model="booking_date_time" x-ref="input" type="text" class="w-full bg-black/5 outline-none p-3" placeholder="Booking Date & Time" />
                        @error('booking_date_time')
                        <span wire:transition.in.duration.500ms="scale-y-100"
                            wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label>Phone Number</label>
                        <input wire:model="phoneno" x-mask="9999999999" class="w-full bg-black/5 outline-none p-3" placeholder="Phone No">
                        <div>
                            @error('phoneno')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label>Number of People</label>
                        <input wire:model="noofpeople" x-mask="99" class="w-full bg-black/5 outline-none p-3" placeholder="No of People">
                        <div>
                            @error('noofpeople')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label>Confirmation Code</label>
                        <input @input="verifyOtp" wire:model="otp" x-mask="999999" class="@if(!$generatedOtp) pointer-events-none opacity-50 @endif w-full bg-black/5 outline-none p-3" placeholder="Confirmation Code">
                        <div class="w-1/2 mx-auto">
                            @error('otp')
                            <span wire:transition.in.duration.500ms="scale-y-100"
                                wire:transition.out.duration.500ms="scale-y-0" class="text-red-700">{{ $message }}</span>
                            @enderror
                        </div>
                        @if($generatedOtp)
                        <div :class="formattedTime == '00:00' && 'text-red-500'" class="mx-auto w-1/2" x-text="formattedTime == '00:00' ? 'Otp Timed Out' : formattedTime"></div>
                        @endif
                    </div>
                    <button type="submit" :class="interval && 'pointer-events-none opacity-50'" class="font-avenir-next-rounded-extra-light uppercase text-center py-2 px-4 bg-primary mx-auto text-white text-xl">{{$generatedOtp ? 'Resend Code' : 'Send Code'}}</button>
                </div>
            </form>
        </div>
    </div>
</div>