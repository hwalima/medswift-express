<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data x-bind:class="$store.theme.dark ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'MedSwift Express'))</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Setting::get('favicon_path', '/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-surface-dark text-gray-900 dark:text-gray-100 transition-colors duration-200">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- ─── Sidebar ─────────────────────────────────────────── --}}
    <aside id="sidebar"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col
                  bg-surface-dark dark:bg-[#080f10]
                  border-r border-white/8 shadow-2xl
                  transform transition-transform duration-200 ease-in-out
                  lg:static lg:inset-auto lg:translate-x-0">

        {{-- Sidebar header --}}
        <div class="flex items-center justify-between h-16 px-4 border-b border-white/10 shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <img src="{{ \App\Models\Setting::get('logo_path', '/images/logo.png') }}"
                     alt="{{ \App\Models\Setting::get('app_name', 'MedSwift Express') }}"
                     class="h-8 w-auto">
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-white/50 hover:text-white p-1">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Nav items --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">

            @php $role = auth()->user()?->role ?? 'client'; @endphp

            {{-- Client links --}}
            @if ($role === 'client')
                <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="chart-bar">Dashboard</x-sidebar-link>
                <x-sidebar-link href="{{ route('shipments.index') }}" :active="request()->routeIs('shipments.*')" icon="cube">My Shipments</x-sidebar-link>
                <x-sidebar-link href="{{ route('shipments.create') }}" :active="false" icon="plus-circle">Book Pickup</x-sidebar-link>
            @endif

            {{-- Admin links --}}
            @if ($role === 'admin')
                <x-sidebar-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" icon="chart-bar">Operations</x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.shipments.index') }}" :active="request()->routeIs('admin.shipments.*')" icon="cube">Shipments</x-sidebar-link>
                <x-sidebar-link href="#" :active="false" icon="user-group">Users</x-sidebar-link>
                <x-sidebar-link href="#" :active="false" icon="chart-pie">Reports</x-sidebar-link>
            @endif

            {{-- Courier links --}}
            @if ($role === 'courier')
                <x-sidebar-link href="{{ route('courier.dashboard') }}" :active="request()->routeIs('courier.dashboard')" icon="truck">Dispatch</x-sidebar-link>
                <x-sidebar-link href="#" :active="false" icon="map">My Routes</x-sidebar-link>
            @endif

            {{-- Shared --}}
            <div class="pt-2 border-t border-white/10 mt-2 space-y-1">
                <x-sidebar-link href="{{ route('ai.chat') }}" :active="request()->routeIs('ai.*')" icon="sparkles">Swiftie AI</x-sidebar-link>
                <x-sidebar-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.*')" icon="user">Profile</x-sidebar-link>
                @if ($role === 'admin')
                    <x-sidebar-link href="{{ route('admin.settings') }}" :active="request()->routeIs('admin.settings*')" icon="cog">Settings</x-sidebar-link>
                @endif
            </div>
        </nav>

        {{-- Sidebar footer --}}
        <div class="shrink-0 px-3 py-4 border-t border-white/10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 min-w-0">
                    <div class="h-8 w-8 rounded-full bg-teal/20 flex items-center justify-center text-teal-light text-sm font-bold shrink-0">
                        {{ substr(auth()->user()?->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()?->name }}</p>
                        <p class="text-xs text-white/50 capitalize">{{ auth()->user()?->role }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign out"
                            class="text-white/40 hover:text-red-400 transition-colors p-1">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Sidebar overlay (mobile) --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"></div>

    {{-- ─── Main content area ───────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top bar --}}
        <header class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 sm:px-6
                        bg-white dark:bg-surface-dark-card border-b border-gray-200 dark:border-gray-700/50
                        backdrop-blur shrink-0">
            <div class="flex items-center gap-4">
                {{-- Hamburger (mobile) --}}
                <button @click="sidebarOpen = true"
                        class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
                <h1 class="font-semibold text-gray-800 dark:text-gray-100">
                    @yield('page-title', config('app.name'))
                </h1>
            </div>

            <div class="flex items-center gap-3">
                {{-- Flash success --}}
                @if (session('success'))
                    <span class="hidden sm:inline-flex items-center gap-1 text-xs text-emerald-dark dark:text-emerald-light bg-emerald/10 px-3 py-1 rounded-full">
                        ✓ {{ session('success') }}
                    </span>
                @endif

                {{-- Dark mode toggle --}}
                <button x-on:click="$store.theme.toggle()"
                        class="rounded-full p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors"
                        aria-label="Toggle dark mode">
                    <svg x-show="$store.theme.dark" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 1 0 0 10A5 5 0 0 0 12 7z"/>
                    </svg>
                    <svg x-show="!$store.theme.dark" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </button>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            @isset($header)
                <div class="mb-6">{{ $header }}</div>
            @endisset
            {{ $slot }}
        </main>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
            dark: localStorage.getItem('medswift-theme') === 'dark'
                    || (!localStorage.getItem('medswift-theme')
                        && window.matchMedia('(prefers-color-scheme: dark)').matches),
            toggle() {
                this.dark = !this.dark;
                localStorage.setItem('medswift-theme', this.dark ? 'dark' : 'light');
            },
        });
    });
</script>
</body>
</html>
