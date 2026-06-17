<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} — Dravion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="font" type="font/woff2" crossorigin
        href="https://fonts.gstatic.com/s/onest/v9/gNMKW3F-SZuj7xmf-HY.woff2">
    {{-- Reserve sidebar width before Alpine initialises — prevents layout shift --}}
    <script>
    (function(){var w=localStorage.getItem('sidebar')==='closed'?'52px':'220px';document.documentElement.style.setProperty('--sidebar-init-w',w);})();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full" style="background:#0a0a0b; color:#e2e2e9;"
    x-data="{ sidebarOpen: localStorage.getItem('sidebar') !== 'closed' }"
    x-init="$watch('sidebarOpen', v => localStorage.setItem('sidebar', v ? 'open' : 'closed'))">

<div class="flex h-full">

    {{-- SIDEBAR --}}
    <aside class="sidebar flex flex-col flex-shrink-0 h-full border-r overflow-hidden"
        style="background:#111113; border-color:#2a2a35; width:var(--sidebar-init-w,220px);"
        :style="sidebarOpen ? 'width:220px' : 'width:52px'">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-3 py-4 border-b" style="border-color:#2a2a35; height:52px;">
            <div class="flex-shrink-0 w-7 h-7 rounded-md flex items-center justify-center text-white text-sm font-bold"
                style="background:#5e6ad2;">D</div>
            <span class="sidebar-label font-semibold text-sm overflow-hidden whitespace-nowrap"
                style="color:#e2e2e9;"
                :style="sidebarOpen ? 'opacity:1;width:auto' : 'opacity:0;width:0'">Dravion</span>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard',    'label' => __('nav.dashboard'), 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                    ['route' => 'admin.users.index',  'label' => __('nav.users'),     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                    ['route' => 'admin.settings',     'label' => __('nav.settings'),  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                    ['route' => 'admin.activity',     'label' => __('nav.activity'),  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
                ];
            @endphp

            @foreach($navItems as $item)
            @php $active = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
                class="flex items-center gap-3 px-2 py-2 rounded-md text-sm transition-colors group"
                style="{{ $active ? 'background:#1e1e2e; color:#e2e2e9;' : 'color:#6b6b7b;' }}"
                onmouseover="if(!{{ $active ? 'true' : 'false' }})this.style.background='#1a1a1f'; this.style.color='#e2e2e9';"
                onmouseout="if(!{{ $active ? 'true' : 'false' }})this.style.background=''; this.style.color='#6b6b7b';"
                title="{{ $item['label'] }}">
                <svg class="flex-shrink-0 w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    {!! $item['icon'] !!}
                </svg>
                <span class="sidebar-label whitespace-nowrap overflow-hidden"
                    :style="sidebarOpen ? 'opacity:1;width:auto' : 'opacity:0;width:0'">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </nav>

        {{-- User + collapse --}}
        <div class="border-t px-2 py-3 space-y-1" style="border-color:#2a2a35;">
            <div class="flex items-center gap-3 px-2 py-2 rounded-md text-xs" style="color:#6b6b7b;">
                <div class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-semibold"
                    style="background:#5e6ad2;">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <span class="sidebar-label overflow-hidden whitespace-nowrap truncate"
                    :style="sidebarOpen ? 'opacity:1;width:auto' : 'opacity:0;width:0'">
                    {{ auth()->user()->name }}
                </span>
            </div>

            <button @click="sidebarOpen = !sidebarOpen"
                class="flex items-center gap-3 w-full px-2 py-2 rounded-md text-sm transition-colors"
                style="color:#6b6b7b;"
                onmouseover="this.style.background='#1a1a1f';this.style.color='#e2e2e9'"
                onmouseout="this.style.background='';this.style.color='#6b6b7b'">
                <svg class="flex-shrink-0 w-4 h-4 transition-transform" :class="sidebarOpen ? '' : 'rotate-180'"
                    fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
                <span class="sidebar-label whitespace-nowrap overflow-hidden"
                    :style="sidebarOpen ? 'opacity:1;width:auto' : 'opacity:0;width:0'">Collapse</span>
            </button>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="flex flex-col flex-1 min-w-0 h-full overflow-hidden">

        {{-- TOPBAR --}}
        <header class="flex items-center justify-between px-6 border-b flex-shrink-0"
            style="height:52px; background:#111113; border-color:#2a2a35;">
            <div>
                <h1 class="text-sm font-semibold" style="color:#e2e2e9;">{{ $title ?? 'Dashboard' }}</h1>
                @isset($breadcrumb)
                <p class="text-xs mt-0.5" style="color:#6b6b7b;">{{ $breadcrumb }}</p>
                @endisset
            </div>

            <div class="flex items-center gap-3">
                {{-- Flash success --}}
                @if(session('success'))
                <span class="text-xs px-2 py-1 rounded-md" style="background:#14532d20; color:#4ade80; border:1px solid #14532d;">
                    {{ session('success') }}
                </span>
                @endif

                {{-- Clear cache --}}
                <form method="POST" action="{{ route('admin.cache.clear') }}">
                    @csrf
                    <button type="submit" title="Clear cache"
                        style="display:flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:7px; border:1px solid #2a2a35; background:transparent; cursor:pointer; color:#6b6b7b;"
                        onmouseover="this.style.background='#1a1a1f';this.style.color='#e2e2e9'"
                        onmouseout="this.style.background='transparent';this.style.color='#6b6b7b'">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="1 4 1 10 7 10"/><polyline points="23 20 23 14 17 14"/>
                            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                        </svg>
                    </button>
                </form>

                {{-- User dropdown --}}
                <x-ui.dropdown align="right">
                    <x-slot:trigger>
                        <div class="flex items-center gap-2 px-2 py-1.5 rounded-md cursor-pointer"
                            style="border:1px solid #2a2a35;"
                            onmouseover="this.style.background='#1a1a1f'"
                            onmouseout="this.style.background='transparent'">
                            <x-ui.avatar :name="auth()->user()->name" :size="24" />
                            <div class="text-left leading-tight">
                                <p class="text-xs font-medium" style="color:#e2e2e9;">{{ auth()->user()->name }}</p>
                                <p class="text-xs" style="color:#4a5a7a;">{{ auth()->user()->getRoleNames()->first() ?? 'user' }}</p>
                            </div>
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#4a5a7a" stroke-width="2.5"><path d="M6 9l6 6 6-6"/></svg>
                        </div>
                    </x-slot:trigger>

                    <form method="POST" action="{{ route('logout') }}" id="topbar-logout">@csrf</form>
                    <button type="button" onclick="document.getElementById('topbar-logout').submit()"
                        style="display:block; width:100%; text-align:left; padding:7px 10px; color:#f87171; font-size:12px; background:transparent; border:none; border-radius:5px; cursor:pointer; font-family:Inter,system-ui;"
                        onmouseover="this.style.background='#1a1a1f'"
                        onmouseout="this.style.background='transparent'">Sign out</button>
                </x-ui.dropdown>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>
</div>

{{-- Global Confirm Modal --}}
<div x-data="{
    open: false,
    title: '',
    message: '',
    formId: '',
    show(title, message, formId) {
        this.title = title;
        this.message = message;
        this.formId = formId;
        this.open = true;
    },
    confirm() {
        document.getElementById(this.formId)?.submit();
        this.open = false;
    }
}" @confirm-action.window="show($event.detail.title, $event.detail.message, $event.detail.formId)"
   x-show="open" x-cloak @keydown.escape.window="open = false"
   class="fixed inset-0 z-[99999] flex items-center justify-center p-5">

    <div @click="open = false" x-show="open"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/60"></div>

    <div @click.stop x-show="open"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-md rounded-2xl border border-gray-800 bg-gray-900 p-6 shadow-2xl">

        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-full bg-error-500/10 border border-error-500/20">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="text-error-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-semibold text-white/90 mb-1" x-text="title"></h3>
                <p class="text-sm text-gray-400" x-text="message"></p>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button @click="open = false"
                class="inline-flex items-center rounded-lg border border-gray-700 bg-transparent px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 transition-colors">
                {{ __('app.cancel') }}
            </button>
            <button @click="confirm()"
                class="inline-flex items-center rounded-lg bg-error-600 px-4 py-2 text-sm font-medium text-white hover:bg-error-700 transition-colors">
                {{ __('app.delete') }}
            </button>
        </div>
    </div>
</div>

</body>
</html>
