<?php

use function Livewire\Volt\{state};

//

?>

<div>
    <div class="px-6 my-32 flex justify-evenly items-center gap-6 *:h-[90vh] *:w-full *:bg-cover *:bg-center *:bg-no-repeat">
        <div style="background-image: url('{{ asset('images/home_1.webp') }}');"></div>
        <div style="background-image: url('{{ asset('images/home_2.webp') }}');"></div>
        <div style="background-image: url('{{ asset('images/home_3.webp') }}');"></div>
        <div style="background-image: url('{{ asset('images/home_4.webp') }}');"></div>
    </div>
    <div class="my-10 flex flex-col gap-4 text-center text-primary">
        <div class="font-avenir-next-rounded-light text-3xl tracking-wider">We are a Pottery Studio offering Memberships and Classes</div>
        <div class="font-avenir-next-regular text-opacity-80 text-lg">
            We offer memberships at all our locations, and classes begin monthly. Our studios are open spaces in with plenty of room to create <br>
            on the wheel or handbuild. We fire to Cone 10 and offer members 24/7 access. You can visit our studios for a tour, just send us a <br>
            note to let us know you are coming to say hello and we'll show you around.
        </div>
    </div>
    <div class="my-32 flex flex-col gap-12 text-center text-primary">
        <div class="flex flex-col gap-2">
            <div class="font-avenir-next-rounded-light text-xl tracking-wider uppercase">HELLO SAN FRANCISCO</div>
            <div class="font-avenir-next-regular text-opacity-80 tracking-wide">
                Please get in touch to come see our San Francisco studio at 2394 Folsom Street 94110
            </div>
        </div>
        <div class="flex justify-center">
            <img class="w-3/4" src="{{ asset('images/home_5.webp') }}">
        </div>
    </div>
    <div class="flex flex-col gap-4 text-primary my-12">
        <div class="font-avenir-next-rounded-light text-center text-lg tracking-wider">BOOK CLASSES AT OUR SF STUDIO</div>
        <div class="w-3/4 flex mx-auto gap-8">
            <template x-for="i in 5">
                <div class="flex flex-col gap-4 group">
                    <img class="w-full aspect-square group-hover:opacity-85" src="{{ asset('images/home_6.webp') }}">
                    <div class="font-avenir-next-rounded-light tracking-wide leading-loose">
                        <div class="text-sm group-hover:underline underline-offset-4">
                            San Francisco February <br> Sundays 11am-1pm: <br> Handbuilding Exploration
                        </div>
                        <div>$225</div>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <div class="my-32 flex justify-center">
        <button class="bg-primary bg-opacity-90 hover:bg-opacity-100 text-white py-3 px-4 uppercase font-avenir-next-rounded-extra-light tracking-wider">See More</button>
    </div>
    <div class="my-32 flex flex-col gap-12 text-center text-primary">
        <div class="flex flex-col gap-2">
            <div class="font-avenir-next-rounded-light text-2xl tracking-wider">BUCKTOWN, CHICAGO</div>
            <div class="font-avenir-next-regular text-opacity-80 tracking-wide leading-relaxed">
                Our location at 2525 N. Elston Ave Chicago IL 60647 fires cone 10 gas reduction, and offers memberships and classes in a beautiful historic building <br>
                with views of the river. If you are interested in working with us, teaching, or getting more information please email <br>
                clayandsupply@thepotterystudio.com.
            </div>
        </div>
        <div class="flex justify-center">
            <img class="w-3/4" src="{{ asset('images/home_7.webp') }}">
        </div>
    </div>
    <div class="flex flex-col gap-4 text-primary my-12">
        <div class="font-avenir-next-rounded-light text-center text-lg tracking-wider uppercase">Book Chicago Classes</div>
        <div class="w-3/4 flex mx-auto gap-8">
            <template x-for="i in 5">
                <div class="flex flex-col gap-4 group">
                    <img class="w-full aspect-square group-hover:opacity-85" src="{{ asset('images/home_6.webp') }}">
                    <div class="font-avenir-next-rounded-light tracking-wide leading-loose">
                        <div class="text-sm group-hover:underline underline-offset-4">
                            San Francisco February <br> Sundays 11am-1pm: <br> Handbuilding Exploration
                        </div>
                        <div>$225</div>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <div class="my-32 flex justify-center">
        <button class="bg-primary bg-opacity-90 hover:bg-opacity-100 text-white py-3 px-4 uppercase font-avenir-next-rounded-extra-light tracking-wider">See More</button>
    </div>
    <div class="my-32 flex flex-col gap-12 text-center text-primary">
        <div class="flex flex-col gap-2">
            <div class="font-avenir-next-rounded-light text-2xl tracking-wider">VISIT OUR COSTA MESA STUDIO</div>
        </div>
        <div class="flex justify-center">
            <img class="w-3/4" src="{{ asset('images/home_8.webp') }}">
        </div>
    </div>
    <div class="flex flex-col gap-4 text-primary my-12">
        <div class="font-avenir-next-rounded-light text-center text-lg tracking-wider uppercase">BOOK COSTA MESA CLASSES</div>
        <div class="w-3/4 flex mx-auto gap-8">
            <template x-for="i in 5">
                <div class="flex flex-col gap-4 group">
                    <img class="w-full aspect-square group-hover:opacity-85" src="{{ asset('images/home_6.webp') }}">
                    <div class="font-avenir-next-rounded-light tracking-wide leading-loose">
                        <div class="text-sm group-hover:underline underline-offset-4">
                            San Francisco February <br> Sundays 11am-1pm: <br> Handbuilding Exploration
                        </div>
                        <div>$225</div>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <div class="my-32 flex justify-center">
        <button class="bg-primary bg-opacity-90 hover:bg-opacity-100 text-white py-3 px-4 uppercase font-avenir-next-rounded-extra-light tracking-wider">See More</button>
    </div>
    <div class="my-32 px-6 text-primary">
        <div class="py-12 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/home_9.jpg') }}');">
            <div class="w-4/5 mx-auto flex">
                <div class="bg-white flex flex-col gap-4 py-8 px-10 text-center">
                    <div class="font-avenir-next-rounded-light">TOUR HOURS</div>
                    <template x-for="i in 6">
                        <div class="font-avenir-next-rounded-light">Cypress Park: Tues, Thurs, Sat 10-2</div>
                    </template>
                    <div class="pt-4">
                        <button class="flex justify-evenly items-center gap-2 py-1 px-4 border border-primary w-min whitespace-nowrap mx-auto text-sm">
                            <div>
                                <svg class="size-4 text-primary" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M11.906 1.994a8.002 8.002 0 0 1 8.09 8.421 7.996 7.996 0 0 1-1.297 3.957.996.996 0 0 1-.133.204l-.108.129c-.178.243-.37.477-.573.699l-5.112 6.224a1 1 0 0 1-1.545 0L5.982 15.26l-.002-.002a18.146 18.146 0 0 1-.309-.38l-.133-.163a.999.999 0 0 1-.13-.202 7.995 7.995 0 0 1 6.498-12.518ZM15 9.997a3 3 0 1 1-5.999 0 3 3 0 0 1 5.999 0Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="text-primary text-sm uppercase font-avenir-next-rounded-light">
                                Get Direction
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="my-32 w-3/4 mx-auto flex flex-col gap-4 text-primary">
        <div class="text-2xl font-avenir-next-rounded-extra-light">Accessibility Statement</div>
        <div class="font-avenir-next-rounded-semibold">
            The Pottery Studio is committed to ensuring digital accessibility for people with disabilities. We are continually improving the user <br>
            experience for everyone, and applying the relevant accessibility standards. If you have difficulty accessing any material on this site, please <br>
            contact us in writing and we will work with you to make the information available. Visit our contact page.
        </div>
    </div>
</div>