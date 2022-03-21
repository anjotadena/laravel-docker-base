<div
    x-data="{
        showSubscribeDialog: false,
        showSuccess: false
    }"
    class="flex flex-col bg-white w-full h-screen"
>
    <x-navigation></x-navigation>

    <div class="flex container mx-auto items-center h-full">
        <div class="flex flex-col w-1/3 items-start">
            <h1 class="text-white font-bold text-5xl leading-tight mb-4">
                Simple landing page to subscribe
            </h1>
            <p class="text-indigo-200 text-xl mb-10">
                We are just checking the <span class="font-bold underline">TALL</span> stack. Would you mind subscribing?
            </p>
            <x-button @click="showSubscribeDialog = true" class="py-3 px-8 bg-red-500 hover:bg-red-600">
                Subscribe
            </x-button>
        </div>    
    </div>

    <x-subscribe-modal></x-subscribe-modal>

    <x-alert-modal></x-alert-modal>
</div>