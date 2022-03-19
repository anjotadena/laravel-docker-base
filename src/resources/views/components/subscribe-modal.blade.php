<x-modal class="!bg-pink-500" trigger="showSubscribeDialog">
    <p class="text-white text-5xl font-extrabold text-center">
        Let's do it!
    </p>
    <form wire:submit.prevent="subscribe" class="flex flex-col items-center p-24">
        <x-input
            class="px-5 py-3 w-80 border border-blue-400"
            type="email"
            name="email"
            placeholder="Email Address"
            wire:model="email"
            required
        ></x-input>
        <span class="text-gray-100 text-xs pt-3">We will send you a confirmation email.</span>
        <x-button class="px-5 py-3 mt-5 w-80 bg-blue-500 justify-center">Get In</x-button>
    </form>
</x-modal>