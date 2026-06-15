<!DOCTYPE html>
<html lang="{{ str_replace(''_'', ''-'', app()->getLocale()) }}"
      x-data
      x-bind:class="$store.theme && $store.theme.dark ? ''dark'' : ''''"
      class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config(''app.name'') }}</title>
    @vite([''resources/css/app.css'', ''resources/js/app.js''])
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script>
        // Apply dark mode before paint to avoid flash
        (function () {
            var theme = localStorage.getItem(''theme'');
            if (theme === ''dark'' || (!theme && window.matchMedia(''(prefers-color-scheme: dark)'').matches)) {
                document.documentElement.classList.add(''dark'');
            }
        })();
    </script>
</head>
<body class="min-h-full bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 flex flex-col">

{{-- NAV --}}
<header class="sticky top-0 z-40 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm"
        x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route(''home'') }}"
               class="text-lg font-bold text-gray-900 dark:text-white tracking-tight flex-shrink-0">
                {{ config(''app.name'') }}
            </a>

            {{-- Desktop nav --}}
            @php $navPages = $navPages ?? []; @endphp
            <nav class="hidden md:flex items-center gap-6">
                @foreach ($navPages as $navPage)
                    <a href="{{ route(''page.show'', $navPage->slug) }}"
                       class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        {{ $navPage->title }}
                    </a>
                @endforeach
            </nav>

            {{-- Desktop auth buttons --}}
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @if(auth()->user()->hasAnyRole([''admin'', ''manager'', ''editor'']))
                        <a href="{{ route(''admin.dashboard'') }}"
                           class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
                            {{ __(''nav.dashboard'') }}
                        </a>
                    @else
                        <a href="{{ route(''dashboard'') }}"
                           class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
                            {{ __(''nav.portal'') }}
                        </a>
                    @endif
                @else
                    <a href="{{ route(''login'') }}"
                       class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        {{ __(''auth.login'') }}
                    </a>
                    <a href="{{ route(''register'') }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
                        {{ __(''auth.register'') }}
                    </a>
                @endauth

                {{-- Dark mode toggle --}}
                <button type="button"
                        onclick="(function(){var t=localStorage.getItem(''theme'');var d=t===''dark''||(!t&&window.matchMedia(''(prefers-color-scheme: dark)'').matches);if(d){localStorage.setItem(''theme'',''light'');document.documentElement.classList.remove(''dark'');}else{localStorage.setItem(''theme'',''dark'');document.documentElement.classList.add(''dark'');}})()"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        aria-label="{{ __(''app.toggle_dark_mode'') }}">
                    <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile hamburger --}}
            <button type="button"
                    class="md:hidden p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    x-on:click="open = !open"
                    :aria-expanded="open">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-cloak x-transition
         class="md:hidden border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="px-4 py-3 space-y-1">
            @foreach ($navPages as $navPage)
                <a href="{{ route(''page.show'', $navPage->slug) }}"
                   class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    {{ $navPage->title }}
                </a>
            @endforeach
            <div class="pt-2 border-t border-gray-200 dark:border-gray-700 space-y-1">
                @auth
                    @if(auth()->user()->hasAnyRole([''admin'', ''manager'', ''editor'']))
                        <a href="{{ route(''admin.dashboard'') }}"
                           class="block px-3 py-2 rounded-lg text-sm font-semibold text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            {{ __(''nav.dashboard'') }}
                        </a>
                    @else
                        <a href="{{ route(''dashboard'') }}"
                           class="block px-3 py-2 rounded-lg text-sm font-semibold text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            {{ __(''nav.portal'') }}
                        </a>
                    @endif
                @else
                    <a href="{{ route(''login'') }}"
                       class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        {{ __(''auth.login'') }}
                    </a>
                    <a href="{{ route(''register'') }}"
                       class="block px-3 py-2 rounded-lg text-sm font-semibold text-blue-600 dark:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        {{ __(''auth.register'') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>

{{-- Main content --}}
<main class="flex-1">
    @yield(''content'')
</main>

{{-- Footer --}}
<footer class="border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500 dark:text-gray-400">
        &copy; {{ date(''Y'') }} {{ config(''app.name'') }}. {{ __(''app.all_rights_reserved'') }}
    </div>
</footer>

</body>
</html>