<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} — Dravion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full" style="background:#0a0a0b; color:#e2e2e9; font-family:Inter,system-ui,sans-serif;"
    x-data="{ sidebarOpen: localStorage.getItem('sidebar') !== 'closed' }"
    x-init="$watch('sidebarOpen', v => localStorage.setItem('sidebar', v ? 'open' : 'closed'))">

<div class="flex h-full">

    {{-- SIDEBAR --}}
    <aside class="sidebar flex flex-col flex-shrink-0 h-full border-r overflow-hidden"
        style="background:#111113; border-color:#2a2a35;"
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
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                    ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                    ['route' => 'admin.settings', 'label' => 'Settings', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                    ['route' => 'admin.activity', 'label' => 'Activity Log', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>'],
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

                {{-- Role badge --}}
                <span class="text-xs px-2 py-1 rounded-md font-medium" style="background:#1e1e2e; color:#5e6ad2; border:1px solid #2a2a35;">
                    {{ auth()->user()->getRoleNames()->first() ?? 'user' }}
                </span>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-md transition-colors"
                        style="color:#6b6b7b; border:1px solid #2a2a35;"
                        onmouseover="this.style.background='#1a1a1f';this.style.color='#e2e2e9'"
                        onmouseout="this.style.background='';this.style.color='#6b6b7b'">
                        Sign out
                    </button>
                </form>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>
</div>

</body>
</html>
