<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data x-bind:class="$store.theme.dark ? 'dark' : ''">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'MedSwift Express') }}</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen
             bg-surface-light dark:bg-surface-dark
             flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

    {{-- Dark mode toggle --}}
    <div class="absolute top-4 right-4">
        <button x-on:click="$store.theme.toggle()"
                class="rounded-full p-2 text-slate hover:bg-gray-200 dark:text-slate-light dark:hover:bg-gray-700 transition-colors"
                aria-label="Toggle dark mode">
            <svg x-show="$store.theme.dark" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 1 0 0 10A5 5 0 0 0 12 7z"/>
            </svg>
            <svg x-show="!$store.theme.dark" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
        </button>
    </div>

    {{-- Logo --}}
    <a href="/" class="flex items-center gap-2 mb-6">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-teal text-white font-bold text-base">M</span>
        <span class="font-semibold text-teal dark:text-teal-light text-xl leading-none">
            MedSwift <span class="font-light text-slate dark:text-slate-light">Express</span>
        </span>
    </a>

    {{-- Auth card --}}
    <div class="w-full sm:max-w-md px-6 py-8
                bg-white dark:bg-surface-dark-card
                shadow-lg sm:rounded-2xl border border-gray-100 dark:border-gray-700/50">
        {{ $slot }}
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
