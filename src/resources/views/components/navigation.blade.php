<nav class="flex pt-5 justify-between items-center container mx-auto text-indigo-200">
    <a class="text-4xl font-bold" href="/">
        <x-application-logo class="w-16 h-16 fill-current"></x-application-logo>
    </a>
    <div>
        @auth
            <a href="{{ route('dashboard') }}">Dashboard</a>
        @else
            <a href="{{ route('login') }}">Login</a>
        @endauth
    </div>
</nav>