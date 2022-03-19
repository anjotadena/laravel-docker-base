@props(['trigger'])

<div
    class="flex fixed top:0 bg-gray-900 bg-opacity-60 items-center w-full h-full"
    x-on:click.self="{{ $trigger }} = false" 
    x-on:keydown.escape.window="{{ $trigger }} = false"
    x-show="{{ $trigger }}"
>
    <div {{ $attributes->merge(['class' => "m-auto bg-gray-500 shadow-2xl rounded-xl p-8"]) }}>
        {{ $slot }}
    </div>
</div>