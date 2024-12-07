<?php

use function Livewire\Volt\{state, on};

state(['show' => false, 'name' => '', 'data' => '']);



on(['show-modal' => function ($name, $data) {
    $this->show = true;
    $this->data = $data;
    $this->name = $name;
}]);

on(['hide-modal' => function () {
    $this->reset();
}]);
?>

<div>
    @if($show)
    <div wire:transition.in.opacity.duration.200ms wire:transition.out.opacity.duration.200ms class="fixed inset-0 flex flex-col">
        <div class="m-auto w-1/2 h-min bg-white rounded-xl shadow-lg border border-black/10">
            @if($name == 'update-status')
            <livewire:modal.booking.update-status :data="$data" />
            @elseif($name == 'manage-address')
            <livewire:modal.address.manage-address :data="$data" />
            @endif
        </div>
    </div>
    @endif
</div>