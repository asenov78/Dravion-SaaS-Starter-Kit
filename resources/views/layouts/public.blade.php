<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $appName = \App\Models\Setting::get('app_name', config('app.name')); @endphp
    <title>@yield('meta_title', $appName . ' — SaaS Starter Kit')</title>
    <meta name="description" content="@yield('meta_desc', 'Complete Laravel SaaS Starter Kit')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                init() {
                    const saved = localStorage.getItem('theme');
                    const sys = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    this.theme = saved || sys;
                    this.updateTheme();
                },
                theme: 'light',
                get dark() { return this.theme === 'dark'; },
                toggle() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    this.updateTheme();
                },
                updateTheme() {
                    if (this.theme === 'dark') {
                        document.documentElement.classList.add('dark');
                        document.body.classList.add('dark', 'bg-gray-900');
                    } else {
                        document.documentElement.classList.remove('dark');
                        document.body.classList.remove('dark', 'bg-gray-900');
                    }
                }
            });
        });
    </script>
    <script>
        (function(){
            var t=localStorage.getItem('theme')||(window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');
            if(t==='dark'){document.documentElement.classList.add('dark');document.body&&document.body.classList.add('dark','bg-gray-900');}
        })();
    </script>
</head>
<body class="min-h-screen bg-white dark:bg-gray-dark text-gray-900 dark:text-white antialiased flex flex-col" x-data>

{{-- ══ HEADER — same visual style as admin app-header ══ --}}
<header class="sticky top-0 flex w-full bg-white border-b border-gray-200 z-99999 dark:border-gray-800 dark:bg-gray-900"
        x-data="{ open: false }">
    <div class="flex items-center justify-between w-full px-4 sm:px-6 lg:px-8 py-3 lg:py-4">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            @php $logoPath = \App\Models\Setting::get('logo'); @endphp
            @if($logoPath)
                <img src="{{ url('storage/' . $logoPath) }}" class="h-9 w-auto object-contain" alt="logo">
            @else
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 text-white font-bold text-sm shadow-sm">
                    {{ strtoupper(substr($appName, 0, 1)) }}
                </div>
            @endif
            <span class="text-base font-bold text-gray-800 dark:text-white/90 tracking-tight">{{ $appName }}</span>
        </a>

        {{-- Desktop nav --}}
        @php $navPages = $navPages ?? []; @endphp
        <nav class="hidden lg:flex items-center gap-1">
            <a href="{{ route('home') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-theme-sm font-medium text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-gray-300 transition-colors">
                {{ __('nav.home') }}
            </a>
            @foreach ($navPages as $navPage)
            @php $specialRoutes = ['contact' => 'contact', 'gallery' => 'gallery']; $href = isset($specialRoutes[$navPage->slug]) ? route($specialRoutes[$navPage->slug]) : route('page.show', $navPage->slug); @endphp
            <a href="{{ $href }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg text-theme-sm font-medium text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-gray-900 dark:hover:text-gray-300 transition-colors">
                {{ $navPage->title }}
            </a>
            @endforeach
        </nav>

        {{-- Right side actions --}}
        <div class="flex items-center gap-2 2xsm:gap-3">

            {{-- Language switcher --}}
            @php $allLangs = \App\Models\Language::orderByDesc('is_default')->orderBy('name')->get(); @endphp
            @if($allLangs->count() > 1)
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open"
                    class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-gray-700 h-11 w-11 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                    title="{{ __('app.switch_language') }}">
                    <span class="text-base leading-none">{{ $allLangs->firstWhere('code', app()->getLocale())?->flag ?? $allLangs->first()?->flag ?? '🌐' }}</span>
                </button>
                <div x-show="open" x-transition x-cloak
                    class="absolute right-0 mt-2 w-40 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900 z-50 overflow-hidden">
                    @foreach($allLangs as $lang)
                    <a href="{{ route('locale.switch', $lang->code) }}"
                        class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors {{ app()->getLocale() === $lang->code ? 'font-semibold bg-gray-50 dark:bg-gray-800' : '' }}">
                        <span>{{ $lang->flag }}</span>
                        <span>{{ $lang->name }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Theme toggle — CSS class approach, no Alpine x-show --}}
            <button @click="$store.theme.toggle()"
                class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-gray-700 h-11 w-11 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                :title="$store.theme.dark ? '{{ __('app.light_mode') }}' : '{{ __('app.dark_mode') }}'">
                {{-- Sun icon — shown in dark mode (hidden dark:block) --}}
                <svg class="hidden dark:block fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415ZM10.0009 6.79327C8.22978 6.79327 6.79402 8.22904 6.79402 10.0001C6.79402 11.7712 8.22978 13.207 10.0009 13.207C11.772 13.207 13.2078 11.7712 13.2078 10.0001C13.2078 8.22904 11.772 6.79327 10.0009 6.79327ZM5.29402 10.0001C5.29402 7.40061 7.40135 5.29327 10.0009 5.29327C12.6004 5.29327 14.7078 7.40061 14.7078 10.0001C14.7078 12.5997 12.6004 14.707 10.0009 14.707C7.40135 14.707 5.29402 12.5997 5.29402 10.0001ZM15.9813 5.08035C16.2742 4.78746 16.2742 4.31258 15.9813 4.01969C15.6884 3.7268 15.2135 3.7268 14.9207 4.01969L14.0368 4.90357C13.7439 5.19647 13.7439 5.67134 14.0368 5.96423C14.3297 6.25713 14.8045 6.25713 15.0974 5.96423L15.9813 5.08035ZM18.4577 10.0001C18.4577 10.4143 18.1219 10.7501 17.7077 10.7501H16.4577C16.0435 10.7501 15.7077 10.4143 15.7077 10.0001C15.7077 9.58592 16.0435 9.25013 16.4577 9.25013H17.7077C18.1219 9.25013 18.4577 9.58592 18.4577 10.0001ZM14.9207 15.9806C15.2135 16.2735 15.6884 16.2735 15.9813 15.9806C16.2742 15.6877 16.2742 15.2128 15.9813 14.9199L15.0974 14.036C14.8045 13.7431 14.3297 13.7431 14.0368 14.036C13.7439 14.3289 13.7439 14.8038 14.0368 15.0967L14.9207 15.9806ZM9.99998 15.7088C10.4142 15.7088 10.75 16.0445 10.75 16.4588V17.7088C10.75 18.123 10.4142 18.4588 9.99998 18.4588C9.58577 18.4588 9.24998 18.123 9.24998 17.7088V16.4588C9.24998 16.0445 9.58577 15.7088 9.99998 15.7088ZM5.96356 15.0972C6.25646 14.8043 6.25646 14.3295 5.96356 14.0366C5.67067 13.7437 5.1958 13.7437 4.9029 14.0366L4.01902 14.9204C3.72613 15.2133 3.72613 15.6882 4.01902 15.9811C4.31191 16.274 4.78679 16.274 5.07968 15.9811L5.96356 15.0972ZM4.29224 10.0001C4.29224 10.4143 3.95645 10.7501 3.54224 10.7501H2.29224C1.87802 10.7501 1.54224 10.4143 1.54224 10.0001C1.54224 9.58592 1.87802 9.25013 2.29224 9.25013H3.54224C3.95645 9.25013 4.29224 9.58592 4.29224 10.0001ZM4.9029 5.9637C5.1958 6.25659 5.67067 6.25659 5.96356 5.9637C6.25646 5.6708 6.25646 5.19593 5.96356 4.90303L5.07968 4.01915C4.78679 3.72626 4.31191 3.72626 4.01902 4.01915C3.72613 4.31204 3.72613 4.78692 4.01902 5.07981L4.9029 5.9637Z" fill="currentColor" />
                </svg>
                {{-- Moon icon — shown in light mode (dark:hidden) --}}
                <svg class="dark:hidden fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.4547 11.97L18.1799 12.1611C18.265 11.8383 18.1265 11.4982 17.8401 11.3266C17.5538 11.1551 17.1885 11.1934 16.944 11.4207L17.4547 11.97ZM8.0306 2.5459L8.57989 3.05657C8.80718 2.81209 8.84554 2.44682 8.67398 2.16046C8.50243 1.8741 8.16227 1.73559 7.83948 1.82066L8.0306 2.5459ZM12.9154 13.0035C9.64678 13.0035 6.99707 10.3538 6.99707 7.08524H5.49707C5.49707 11.1823 8.81835 14.5035 12.9154 14.5035V13.0035ZM16.944 11.4207C15.8869 12.4035 14.4721 13.0035 12.9154 13.0035V14.5035C14.8657 14.5035 16.6418 13.7499 17.9654 12.5193L16.944 11.4207ZM16.7295 11.7789C15.9437 14.7607 13.2277 16.9586 10.0003 16.9586V18.4586C13.9257 18.4586 17.2249 15.7853 18.1799 12.1611L16.7295 11.7789ZM10.0003 16.9586C6.15734 16.9586 3.04199 13.8433 3.04199 10.0003H1.54199C1.54199 14.6717 5.32892 18.4586 10.0003 18.4586V16.9586ZM3.04199 10.0003C3.04199 6.77289 5.23988 4.05695 8.22173 3.27114L7.83948 1.82066C4.21532 2.77574 1.54199 6.07486 1.54199 10.0003H3.04199ZM6.99707 7.08524C6.99707 5.52854 7.5971 4.11366 8.57989 3.05657L7.48132 2.03522C6.25073 3.35885 5.49707 5.13487 5.49707 7.08524H6.99707Z" fill="currentColor" />
                </svg>
            </button>

            {{-- Auth buttons / User dropdown --}}
            @auth
                @if(auth()->user()->hasAnyRole(['admin', 'manager', 'editor']))
                    <a href="{{ route('admin.dashboard') }}"
                       class="hidden sm:inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        {{ __('nav.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('home') }}"
                       class="hidden sm:inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 transition-colors">
                        {{ __('nav.portal') }}
                    </a>
                @endif

                {{-- User dropdown --}}
                <div class="relative" x-data="{
                    dropdownOpen: false,
                    toggleDropdown() { this.dropdownOpen = !this.dropdownOpen; },
                    closeDropdown() { this.dropdownOpen = false; }
                }" @click.away="closeDropdown()">
                    <button class="flex items-center text-gray-700 dark:text-gray-400"
                            @click.prevent="toggleDropdown()" type="button">
                        @if(auth()->user()->avatar)
                            <span class="mr-2 overflow-hidden rounded-full h-9 w-9">
                                <img src="{{ url('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="h-9 w-9 object-cover rounded-full">
                            </span>
                        @else
                            <span class="mr-2 overflow-hidden rounded-full h-9 w-9 flex items-center justify-center bg-brand-500 text-white font-semibold text-sm">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </span>
                        @endif
                        <span class="hidden sm:block mr-1 font-medium text-theme-sm">{{ auth()->user()->name ?? 'User' }}</span>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="dropdownOpen" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark z-50"
                         style="display: none;">
                        <div>
                            <span class="block font-medium text-gray-700 text-theme-sm dark:text-gray-400">{{ auth()->user()->name ?? '' }}</span>
                            <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->email ?? '' }}</span>
                        </div>
                        <ul class="flex flex-col gap-1 pt-4 pb-3 border-b border-gray-200 dark:border-gray-800">
                            <li>
                                @if(auth()->user()->hasAnyRole(['admin', 'manager', 'editor']))
                                <a href="{{ route('admin.dashboard') }}"
                                @else
                                <a href="{{ route('home') }}"
                                @endif
                                   class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    <span class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                    </span>
                                    {{ auth()->user()->hasAnyRole(['admin', 'manager', 'editor']) ? __('nav.dashboard') : __('nav.portal') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sessions.index') }}"
                                   class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    <span class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                                    </span>
                                    {{ __('sessions.title') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('api-tokens.index') }}"
                                   class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                    <span class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                                    </span>
                                    {{ __('tokens.title') }}
                                </a>
                            </li>
                        </ul>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" @click="closeDropdown()"
                                class="flex items-center w-full gap-3 px-3 py-2 mt-3 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                <span class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                </span>
                                {{ __('auth.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                   class="hidden sm:flex items-center rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    {{ __('auth.login') }}
                </a>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-brand-600 transition-colors">
                    {{ __('auth.register') }}
                </a>
            @endauth

            {{-- Mobile hamburger --}}
            <button @click="open = !open"
                    class="flex lg:hidden items-center justify-center w-10 h-10 rounded-lg border border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-cloak x-transition
         class="absolute top-full left-0 right-0 lg:hidden border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-lg">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5">{{ __('nav.home') }}</a>
            @foreach ($navPages as $navPage)
            @php $specialRoutesMobile = ['contact' => 'contact', 'gallery' => 'gallery']; $hrefMobile = isset($specialRoutesMobile[$navPage->slug]) ? route($specialRoutesMobile[$navPage->slug]) : route('page.show', $navPage->slug); @endphp
            <a href="{{ $hrefMobile }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5">{{ $navPage->title }}</a>
            @endforeach
            <div class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-800 space-y-1">
                @auth
                    <a href="{{ auth()->user()->hasAnyRole(['admin','manager','editor']) ? route('admin.dashboard') : route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold text-brand-500 hover:bg-gray-100 dark:hover:bg-white/5">
                        {{ auth()->user()->hasAnyRole(['admin','manager','editor']) ? __('nav.dashboard') : __('nav.portal') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="px-3">
                        @csrf
                        <button type="submit" class="w-full text-left py-2 text-sm font-medium text-gray-700 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                            {{ __('auth.logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5">{{ __('auth.login') }}</a>
                    <a href="{{ route('register') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-semibold text-brand-500 hover:bg-gray-100 dark:hover:bg-white/5">{{ __('auth.register') }}</a>
                @endauth
            </div>
        </div>
    </div>
</header>

@php $publicLicensed = !empty(config('dravion.license_key')); @endphp
@if(!$publicLicensed)
<div class="flex items-center justify-center gap-2 bg-amber-50 border-b border-amber-200 dark:bg-amber-900/20 dark:border-amber-800 px-6 py-2 text-xs text-amber-700 dark:text-amber-400">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <span>{{ $appName }} {{ __('license.no_license_portal') }}</span>
</div>
@endif

{{-- Content --}}
<main class="flex-1">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 py-8 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                @if($logoPath ?? false)
                    <img src="{{ url('storage/' . $logoPath) }}" class="h-7 w-auto object-contain" alt="logo">
                @else
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-brand-500 text-white font-bold text-xs">
                        {{ strtoupper(substr($appName, 0, 1)) }}
                    </div>
                @endif
                <span class="text-sm font-bold text-gray-800 dark:text-white/90">{{ $appName }}</span>
            </div>
            @php $footerText = \App\Models\Setting::get('footer_text', ''); @endphp
            @if($footerText)
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center">{{ $footerText }}</p>
            @endif
            <nav class="flex flex-wrap justify-center gap-x-6 gap-y-2">
                @foreach ($navPages as $navPage)
                @php $specialRoutesFooter = ['contact' => 'contact', 'gallery' => 'gallery']; $hrefFooter = isset($specialRoutesFooter[$navPage->slug]) ? route($specialRoutesFooter[$navPage->slug]) : route('page.show', $navPage->slug); @endphp
                <a href="{{ $hrefFooter }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ $navPage->title }}</a>
                @endforeach
                <a href="{{ route('login') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('auth.login') }}</a>
            </nav>
            @php $footerCopyright = \App\Models\Setting::get('footer_copyright', ''); @endphp
            <p class="text-sm text-gray-400 dark:text-gray-500">
                {{ $footerCopyright ?: ('© ' . date('Y') . ' ' . $appName . '. ' . __('app.all_rights_reserved')) }}
            </p>
        </div>
    </div>
</footer>

</body>
</html>