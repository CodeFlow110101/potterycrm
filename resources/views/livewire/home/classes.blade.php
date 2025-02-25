<?php

use function Livewire\Volt\{state};

//

?>

<div class="w-11/12 mx-auto gap-4 lg:gap-8 py-4 lg:py-8 text-white grow flex flex-col">
    <div class="text-5xl lg:text-7xl font-avenir-next-bold">Classes</div>
    <div class="grow relative" x-data="{ height: 0 }" x-resize="height = $height">
        <div class="backdrop-blur-xl border border-white rounded-lg overflow-y-auto hidden-scrollbar absolute inset-x-0 flex flex-col" :style="'height: ' + height + 'px;'">
            <div class="text-xl flex flex-col gap-4 w-11/12 mx-auto py-8 grow">
                <div>
                    At Icona, we are passionate about pottery and creativity. We offer a variety of pottery painting classes designed for all skill levels, from beginners to advanced artists.
                </div>
                <div>
                    Our expert instructors guide you through each step, helping you learn new techniques, improve your skills, and complete a project that you can truly be proud of. Whether you're looking to explore a new hobby or refine your artistic abilities, Icona provides a welcoming space to create, learn, and have fun.
                </div>
                <div>
                    Join us and unleash your creativity today!
                </div>
                <div class="flex flex-col gap-4 grow">
                    <div>Current Classes:</div>
                    <div class="w-full grow border border-white rounded-lg flex justify-center items-center py-12">
                        Coming Soon
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>