<?php

use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{state, layout, mount};

layout('components.layouts.app');

state(['path']);

mount(function () {
    $this->path = request()->path();
    $isAuth = Auth::check();
    if ($isAuth && in_array($this->path, ['/'])) {
        $this->redirectRoute('dashboard', navigate: true);
    } elseif (!$isAuth && in_array($this->path, ['dashboard'])) {
        $this->redirectRoute('/', navigate: true);
    }
});
?>

<div class="h-screen">
    @if($path == '/')
    <livewire:book-table />
    @elseif($path == 'dashboard')
    <div class="flex justify-between h-full">
        <div class="w-1/5 h-full">
            <livewire:side-bar />
        </div>
        <div class="w-4/5">
            <livewire:dashboard />
        </div>
    </div>
    @endif
</div>