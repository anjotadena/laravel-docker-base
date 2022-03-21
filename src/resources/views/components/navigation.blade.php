<nav class="bg-white border-b">
    <div class="flex py-3 justify-between items-center mx-auto text-gray container">
        <div class="flex items-center flex-row">
            <a class="text-3xl font-bold mr-4" href="/">
                <x-application-logo class="w-16 h-16 fill-current"></x-application-logo>
            </a>
            <div class="flex flex-row">
                <a class="px-8 hover:text-gray-600" href="{{ route('dashboard') }}">Home</a>
                <a class="px-8 hover:bg-gray-50" href="{{ route('dashboard') }}">FAQ</a>
                <a class="px-8 hover:bg-gray-100" href="{{ route('dashboard') }}">Contact</a>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard') }}">Sign In</a>
            <a href="{{ route('dashboard') }}">Join Now</a>
        </div>
    </div>
</nav>

