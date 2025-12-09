<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <nav class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex shrink-0 items-center">
                        <h1 class="text-xl font-bold dark:text-white">{{ config('app.name') }}</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-blue-500' : 'border-transparent' }} text-sm font-medium dark:text-gray-300">
                            Dashboard
                        </a>
                        <a href="{{ route('wallet') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('wallet') ? 'border-blue-500' : 'border-transparent' }} text-sm font-medium dark:text-gray-300">
                            Wallet
                        </a>
                        <a href="{{ route('orders') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('orders') ? 'border-blue-500' : 'border-transparent' }} text-sm font-medium dark:text-gray-300">
                            Orders
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="dark:text-gray-300">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:text-red-800">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10" id="app">
        @yield('content')
    </main>
</body>
</html>
