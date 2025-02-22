<?php

use function Livewire\Volt\{state, mount};
use Illuminate\Support\Facades\Mail;


$submit = function () {
    try {
        Mail::raw('This is another test email from Laravel!', function ($message) {
            $message->to(env('TEST_TO_MAIL'))
                ->subject('Test Email from Laravel');
        });
    } catch (\Exception $e) {
        dd($e);
    }
};

?>

<div>
    <button wire:click="submit">click</button>
</div>