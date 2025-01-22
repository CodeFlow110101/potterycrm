<?php

use function Livewire\Volt\{state, mount};

state(['faq' => config('constants.faq')]);

?>

<div class="my-10 w-3/4 mx-auto text-primary">
    <div class="uppercase font-avenir-next-rounded-light text-center my-10 text-primary text-3xl">
        FAQ
    </div>
    <table class="table-auto border border-black/10 divide-y divide-black/10 w-full">
        @foreach($faq as $question => $answer)
        <tr class="divide-x divide-black/10">
            <td class="border border-black/10 p-5 font-avenir-next-rounded-bold">{{ $question }}</td>
            <td class="border border-black/10 p-5 font-avenir-next-rounded-regular">{{ $answer }}</td>
        </tr>
        @endforeach
    </table>

</div>