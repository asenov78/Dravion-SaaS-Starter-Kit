<!DOCTYPE html>
<html lang="en" style="height:100%; overflow:hidden;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} — Dravion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #2a2a35; border-radius: 3px; }
        [x-cloak] { display: none !important; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 10px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            color: #6b6b7b;
            transition: background 0.1s, color 0.1s;
            white-space: nowrap;
            overflow: hidden;
        }
        .nav-link:hover { background: #ffffff08; color: #c2c2ce; }
        .nav-link.active { background: #5e6ad222; color: #818cf8; }
        .nav-link .nav-icon { flex-shrink: 0; width: 16px; height: 16px; }
        .nav-label { overflow: hidden; transition: opacity 0.15s, width 0.2s; }
        .sidebar-collapsed .nav-label { opacity: 0; width: 0; }
        .sidebar-collapsed .nav-section-label { opacity: 0; }
    </style>
</head>
<body style="background:#0a1628 url('/images/bg.jpg') center center / cover no-repeat fixed; color:#e2e2e9; font-family:Inter,system-ui,sans-serif; height:100%; display:flex; overflow:hidden;"
    x-data="{ open: localStorage.getItem('sidebar') !== 'closed' }"
    x-init="$watch('open', v => localStorage.setItem('sidebar', v ? 'open' : 'closed'))"
    :class="open ? '' : 'sidebar-collapsed'">

{{-- ═══════════ SIDEBAR ═══════════ --}}
<aside style="background:transparent; border-right:1px solid #1e1e27; display:flex; flex-direction:column; flex-shrink:0; height:100%; transition:width 0.2s ease; overflow:hidden;"
    :style="{ width: open ? '220px' : '52px' }">

    {{-- Logo --}}
    <div style="display:flex; align-items:center; gap:10px; padding:0 14px; height:52px; border-bottom:1px solid #1e1e27; flex-shrink:0;">
        <div style="width:26px; height:26px; background:#5e6ad2; border-radius:7px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="white">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
            </svg>
        </div>
        <span class="nav-label" style="font-weight:700; font-size:14px; color:#e2e2e9; letter-spacing:-0.01em;">Dravion</span>
    </div>

    {{-- Nav --}}
    <nav style="flex:1; overflow-y:auto; overflow-x:hidden; padding:12px 8px; display:flex; flex-direction:column; gap:1px;">

        {{-- Section label --}}
        <div class="nav-section-label" style="padding:0 10px 6px; font-size:10px; font-weight:600; letter-spacing:0.08em; color:#3a3a45; text-transform:uppercase; transition:opacity 0.15s;" :style="{ opacity: open ? '1' : '0' }">Navigation</div>

        @php
        $nav = [
            ['route' => 'admin.dashboard',    'label' => 'Dashboard',     'icon' => 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z M9 22V12h6v10'],
            ['route' => 'admin.users.index',  'label' => 'Users',         'icon' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M23 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75'],
            ['route' => 'admin.settings',     'label' => 'Settings',      'icon' => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z'],
            ['route' => 'admin.activity',     'label' => 'Activity Log',  'icon' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M16 13H8 M16 17H8 M10 9H8'],
        ];
        @endphp

        @foreach($nav as $item)
        @php $active = request()->routeIs($item['route']); @endphp
        <a href="{{ route($item['route']) }}"
           class="nav-link {{ $active ? 'active' : '' }}"
           title="{{ $item['label'] }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $item['icon'] }}"/>
            </svg>
            <span class="nav-label">{{ $item['label'] }}</span>
        </a>
        @endforeach

    </nav>

    {{-- User + Collapse --}}
    <div style="border-top:1px solid #1e1e27; padding:8px; flex-shrink:0; display:flex; flex-direction:column; gap:2px;">

        {{-- User row --}}
        <div style="display:flex; align-items:center; gap:10px; border-radius:7px; overflow:hidden; cursor:default; padding:6px 10px;"
            :style="{ justifyContent: open ? 'flex-start' : 'center', padding: open ? '6px 10px' : '6px 0' }"
        >
            <div style="width:26px; height:26px; border-radius:50%; background:#5e6ad2; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:#fff; flex-shrink:0;">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="nav-label" style="min-width:0;">
                <p style="color:#c2c2ce; font-size:12px; font-weight:500; margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->user()->name }}</p>
                <p style="color:#4b4b5b; font-size:11px; margin:1px 0 0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->user()->getRoleNames()->first() }}</p>
            </div>
        </div>

        {{-- Collapse button --}}
        <button @click="open = !open"
            style="display:flex; align-items:center; gap:10px; width:100%; padding:6px 10px; border-radius:7px; background:transparent; border:none; cursor:pointer; color:#6b6b7b; font-size:12px; font-family:Inter,system-ui; transition:background 0.1s, color 0.1s;"
            :style="{ justifyContent: open ? 'flex-start' : 'center', padding: open ? '6px 10px' : '6px 0' }"
            onmouseover="this.style.background='#1a1a1f';this.style.color='#c2c2ce'"
            onmouseout="this.style.background='transparent';this.style.color='#6b6b7b'">
            <svg style="flex-shrink:0; width:15px; height:15px; transition:transform 0.2s;"
                :style="{ transform: open ? 'rotate(0deg)' : 'rotate(180deg)' }"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
            <span class="nav-label" style="font-size:12px;">Collapse</span>
        </button>
    </div>
</aside>

{{-- ═══════════ MAIN ═══════════ --}}
<div style="flex:1; display:flex; flex-direction:column; min-width:0; height:100%; overflow:hidden;">

    {{-- TOPBAR --}}
    <header style="height:52px; background:transparent; border-bottom:1px solid #1e1e27; display:flex; align-items:center; justify-content:space-between; padding:0 24px; flex-shrink:0; gap:16px;">

        <div style="display:flex; align-items:center; gap:10px; min-width:0;">
            <h1 style="color:#e2e2e9; font-size:14px; font-weight:600; margin:0; white-space:nowrap;">{{ $title ?? 'Dashboard' }}</h1>
            @isset($breadcrumb)
            <span style="color:#2a2a35; font-size:14px;">/</span>
            <span style="color:#6b6b7b; font-size:12px;">{{ $breadcrumb }}</span>
            @endisset
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
            {{-- Flash --}}
            @if(session('success'))
            <div style="display:flex; align-items:center; gap:6px; background:#14532d20; border:1px solid #14532d50; border-radius:6px; padding:5px 10px; color:#4ade80; font-size:12px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- Role badge --}}
            <x-ui.badge variant="accent">{{ auth()->user()->getRoleNames()->first() ?? 'user' }}</x-ui.badge>

            {{-- Sign out --}}
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <x-ui.button type="submit" variant="ghost" size="sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4 M16 17l5-5-5-5 M21 12H9"/>
                    </svg>
                    Sign out
                </x-ui.button>
            </form>
        </div>
    </header>

    {{-- PAGE --}}
    <main style="flex:1; overflow-y:auto; padding:24px;">
        {{ $slot }}
    </main>
</div>

</body>
</html>
