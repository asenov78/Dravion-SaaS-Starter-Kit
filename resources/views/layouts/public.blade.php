<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('meta_title', config('app.name') . ' — SaaS Starter Kit')</title>
    <meta name="description" content="@yield('meta_desc', 'Complete Laravel SaaS Starter Kit with roles, permissions, licensing, self-updater and more.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .hero-gradient { background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99,102,241,.18), transparent); }
        .feature-card:hover { transform: translateY(-2px); }
        .feature-card { transition: transform .2s ease, box-shadow .2s ease; }
    </style>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const saved = localStorage.getItem('theme');
                    const sys = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    this.theme = saved || sys;
                    this.applyTheme();
                },
                theme: 'light',
                get dark() { return this.theme === 'dark'; },
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.applyTheme();
                },
                applyTheme() {
                    document.documentElement.classList.toggle('dark', this.theme === 'dark');
                }
            });
        });
    </script>
    <script>
        (function(){
            var t=localStorage.getItem('theme')||(window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');
            if(t==='dark') document.documentElement.classList.add('dark');
        })();
    </script>
</head>
<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-white antialiased" x-data>

{{-- ══════════════════ HEADER ══════════════════ --}}
<header class="sticky top-0 z-50 border-b border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-950/80 backdrop-blur-md"
        x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-brand-500 flex items-center justify-center text-white font-bold text-sm">
                    {{ strtoupper(substr(config('app.name', 'D'), 0, 1)) }}
                </div>
                <span class="text-base font-bold text-gray-900 dark:text-white tracking-tight">{{ config('app.name') }}</span>
            </a>

            {{-- Desktop nav --}}
            @php $navPages = $navPages ?? []; @endphp
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}"
                   class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    {{ __('nav.home') }}
                </a>
                @foreach ($navPages as $navPage)
                <a href="{{ route('page.show', $navPage->slug) }}"
                   class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    {{ $navPage->title }}
                </a>
                @endforeach
            </nav>

            {{-- Right side --}}
            <div class="hidden md:flex items-center gap-2">
                {{-- Dark mode toggle --}}
                <button @click="$store.theme.toggle()"
                        class="flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg x-show="!$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="$store.theme.dark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                @auth
                    @if(auth()->user()->hasAnyRole(['admin', 'manager', 'editor']))
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            {{ __('nav.dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600 transition-colors shadow-sm">
                            {{ __('nav.portal') }}
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        {{ __('auth.login') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg bg-brand-500 text-white text-sm font-semibold hover:bg-brand-600 transition-colors shadow-sm">
                        {{ __('auth.register') }}
                    </a>
                @endauth
            </div>

            {{-- Mobile hamburger --}}
            <button @click="open = !open" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-cloak x-transition class="md:hidden border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">{{ __('nav.home') }}</a>
            @foreach ($navPages as $navPage)
            <a href="{{ route('page.show', $navPage->slug) }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">{{ $navPage->title }}</a>
            @endforeach
            <div class="pt-2 border-t border-gray-200 dark:border-gray-700 space-y-1">
                @auth
                    @if(auth()->user()->hasAnyRole(['admin', 'manager', 'editor']))
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-brand-500">{{ __('nav.dashboard') }}</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-brand-500">{{ __('nav.portal') }}</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">{{ __('auth.login') }}</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-brand-500 hover:bg-gray-100 dark:hover:bg-gray-800">{{ __('auth.register') }}</a>
                @endauth
            </div>
        </div>
    </div>
</header>

{{-- Page content --}}
<main>
    @yield('content')
</main>

{{-- ══════════════════ FOOTER ══════════════════ --}}
<footer class="border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-brand-500 flex items-center justify-center text-white font-bold text-xs">
                    {{ strtoupper(substr(config('app.name', 'D'), 0, 1)) }}
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ config('app.name') }}</span>
            </div>
            <nav class="flex flex-wrap justify-center gap-x-6 gap-y-2">
                @foreach ($navPages as $navPage)
                <a href="{{ route('page.show', $navPage->slug) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ $navPage->title }}</a>
                @endforeach
                <a href="{{ route('login') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('auth.login') }}</a>
            </nav>
            <p class="text-sm text-gray-400 dark:text-gray-500">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('app.all_rights_reserved') }}</p>
        </div>
    </div>
</footer>

</body>
</html>