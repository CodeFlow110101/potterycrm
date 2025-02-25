<?php

use function Livewire\Volt\{state, mount};

state(['faq' => config('constants.faq')]);

?>

<div class="w-11/12 mx-auto gap-4 lg:gap-8 py-4 lg:py-8 text-white grow flex flex-col">
    <div class="text-5xl lg:text-7xl font-avenir-next-bold">FAQ</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="backdrop-blur-xl border border-white rounded-lg overflow-y-auto hidden-scrollbar absolute inset-x-0" :style="'height: ' + height + 'px;'">
            <table class="table-auto w-full">
                @foreach($faq as $question => $answer)
                <tr class="divide-x divide-white">
                    <td class="border border-white p-5 font-avenir-next-rounded-bold">{{ $question }}</td>
                    <td class="border border-white p-5 font-avenir-next-rounded-regular text-sm">{{ $answer }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>