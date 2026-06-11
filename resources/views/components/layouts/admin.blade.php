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
        ::-webkit-scrollbar-thumb { background: #1e2a45; border-radius: 3px; }
        [x-cloak] { display: none !important; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 500;
            text-decoration: none;
            color: #7a8aaa;
            transition: background 0.12s, color 0.12s;
            white-space: nowrap;
            overflow: hidden;
            cursor: pointer;
        }
        .nav-link:hover { background: rgba(255,255,255,0.06); color: #c8d0e0; }
        .nav-link.active { background: rgba(94,106,210,0.18); color: #a5b0f5; }
        .nav-link.active .nav-icon-wrap { color: #818cf8; }
        .nav-icon-wrap { flex-shrink:0; width:16px; height:16px; display:flex; align-items:center; justify-content:center; }
        .nav-label { overflow: hidden; transition: opacity 0.15s; white-space:nowrap; }
        .sidebar-collapsed .nav-label { opacity: 0; width: 0; pointer-events:none; }
        .sidebar-collapsed .nav-section-label { opacity: 0 !important; }
        .sidebar-collapsed .sidebar-logo-text { opacity: 0; width: 0; }
        .sidebar-collapsed .sidebar-promo { display: none; }
        .sidebar-collapsed .sidebar-version { opacity: 0; }
        .nav-badge {
            margin-left: auto;
            flex-shrink: 0;
            font-size: 10px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 4px;
            background: rgba(94,106,210,0.25);
            color: #818cf8;
            letter-spacing: 0.03em;
        }
        .nav-dot {
            margin-left: auto;
            flex-shrink: 0;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #f97316;
        }
    </style>
</head>
<body style="background:#060d1a; color:#e2e2e9; font-family:Inter,system-ui,sans-serif; height:100%; display:flex; overflow:hidden; position:relative;"
    x-data="{ open: localStorage.getItem('sidebar') !== 'closed' }"
    x-init="$watch('open', v => localStorage.setItem('sidebar', v ? 'open' : 'closed'))"
    :class="open ? '' : 'sidebar-collapsed'">

<x-ui.net-bg />

{{-- ═══════════ SIDEBAR ═══════════ --}}
<aside style="background:rgba(8,16,36,0.72); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border-right:1px solid rgba(255,255,255,0.06); display:flex; flex-direction:column; flex-shrink:0; height:100%; transition:width 0.22s ease; overflow:hidden; position:relative; z-index:10;"
    :style="{ width: open ? '224px' : '54px' }">

    {{-- ── Logo + Collapse ── --}}
    <div style="display:flex; align-items:center; justify-content:space-between; padding:0 12px; height:56px; border-bottom:1px solid rgba(255,255,255,0.05); flex-shrink:0; gap:8px;">
        <div style="display:flex; align-items:center; gap:9px; min-width:0; overflow:hidden;">
            <div style="width:28px; height:28px; background:linear-gradient(135deg,#5e6ad2,#818cf8); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 2px 8px rgba(94,106,210,0.4);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="white">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                </svg>
            </div>
            <div class="sidebar-logo-text" style="overflow:hidden; transition:opacity 0.15s;">
                <p style="color:#e2e2e9; font-size:13px; font-weight:700; margin:0; letter-spacing:-0.01em; white-space:nowrap;">Dravion</p>
                <p style="color:#4a5a7a; font-size:10px; margin:1px 0 0; white-space:nowrap;">Admin Panel</p>
            </div>
        </div>

        {{-- Collapse button --}}
        <button @click="open = !open"
            style="flex-shrink:0; width:22px; height:22px; border-radius:6px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); cursor:pointer; display:flex; align-items:center; justify-content:center; color:#4a5a7a; transition:background 0.1s, color 0.1s; padding:0;"
            onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='#c8d0e0'"
            onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.color='#4a5a7a'">
            <svg style="width:12px; height:12px; transition:transform 0.22s; flex-shrink:0;"
                :style="{ transform: open ? 'rotate(0deg)' : 'rotate(180deg)' }"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
        </button>
    </div>

    {{-- ── Nav ── --}}
    <nav style="flex:1; overflow-y:auto; overflow-x:hidden; padding:10px 8px; display:flex; flex-direction:column; gap:1px;">

        @php
        $sections = [
            'GENERAL' => [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z M9 22V12h6v10'],
            ],
            'TOOLS' => [
                ['route' => 'admin.users.index', 'label' => 'Users', 'icon' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M23 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75'],
                ['route' => 'admin.activity',    'label' => 'Activity', 'icon' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M16 13H8 M16 17H8 M10 9H8'],
            ],
            'SUPPORT' => [
                ['route' => 'admin.settings', 'label' => 'Settings', 'icon' => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z'],
            ],
        ];
        @endphp

        @foreach($sections as $section => $items)
        <div class="nav-section-label" style="padding:10px 10px 5px; font-size:10px; font-weight:600; letter-spacing:0.09em; color:#2a3a55; text-transform:uppercase; transition:opacity 0.15s;" :style="{ opacity: open ? '1' : '0' }">{{ $section }}</div>

        @foreach($items as $item)
        @php $active = request()->routeIs($item['route']); @endphp
        <a href="{{ route($item['route']) }}"
           class="nav-link {{ $active ? 'active' : '' }}"
           title="{{ $item['label'] }}">
            <span class="nav-icon-wrap">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $item['icon'] }}"/>
                </svg>
            </span>
            <span class="nav-label">{{ $item['label'] }}</span>
            @if(!empty($item['badge']))
            <span class="nav-badge nav-label">{{ $item['badge'] }}</span>
            @endif
        </a>
        @endforeach
        @endforeach

    </nav>

    {{-- ── Promo card ── --}}
    <div class="sidebar-promo" style="margin:0 8px 8px; padding:14px; background:linear-gradient(135deg,rgba(94,106,210,0.2),rgba(129,140,248,0.1)); border:1px solid rgba(94,106,210,0.25); border-radius:10px; flex-shrink:0;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
            <div style="width:30px; height:30px; background:linear-gradient(135deg,#5e6ad2,#818cf8); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z M2 17l10 5 10-5 M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div>
                <p style="color:#c8d0e0; font-size:11.5px; font-weight:600; margin:0;">Installer Wizard</p>
                <p style="color:#4a5a7a; font-size:10px; margin:1px 0 0;">Coming soon</p>
            </div>
        </div>
        <a href="#" style="display:block; text-align:center; background:linear-gradient(135deg,#5e6ad2,#818cf8); color:#fff; font-size:11px; font-weight:600; padding:7px; border-radius:7px; text-decoration:none; transition:opacity 0.1s;"
            onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
            Get Started →
        </a>
    </div>

    {{-- ── User ── --}}
    <div style="border-top:1px solid rgba(255,255,255,0.05); padding:8px; flex-shrink:0;">
        <div style="display:flex; align-items:center; gap:9px; padding:7px 8px; border-radius:8px; overflow:hidden;"
            :style="{ justifyContent: open ? 'flex-start' : 'center' }">
            <div style="width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg,#5e6ad2,#818cf8); display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:#fff; flex-shrink:0;">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="nav-label" style="min-width:0; flex:1;">
                <p style="color:#c8d0e0; font-size:12px; font-weight:500; margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->user()->name }}</p>
                <p style="color:#4a5a7a; font-size:10px; margin:1px 0 0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ auth()->user()->getRoleNames()->first() }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0; flex-shrink:0;" class="nav-label">
                @csrf
                <button type="submit" title="Sign out"
                    style="background:none; border:none; cursor:pointer; color:#4a5a7a; padding:4px; border-radius:5px; display:flex; align-items:center; transition:color 0.1s;"
                    onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='#4a5a7a'">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4 M16 17l5-5-5-5 M21 12H9"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- ── Version ── --}}
    <div class="sidebar-version" style="padding:6px 16px 10px; transition:opacity 0.15s;" :style="{ opacity: open ? '1' : '0' }">
        <p style="color:#2a3a55; font-size:10px; margin:0;">© {{ date('Y') }} Dravion · v{{ config('dravion.version') }}</p>
    </div>

</aside>

{{-- ═══════════ MAIN ═══════════ --}}
<div style="flex:1; display:flex; flex-direction:column; min-width:0; height:100%; overflow:hidden; position:relative; z-index:1;">

    {{-- TOPBAR --}}
    <header style="height:52px; background:rgba(6,13,26,0.5); backdrop-filter:blur(8px); border-bottom:1px solid rgba(255,255,255,0.05); display:flex; align-items:center; justify-content:space-between; padding:0 24px; flex-shrink:0; gap:16px;">

        <div style="display:flex; align-items:center; gap:10px; min-width:0;">
            <h1 style="color:#e2e2e9; font-size:14px; font-weight:600; margin:0; white-space:nowrap;">{{ $title ?? 'Dashboard' }}</h1>
            @isset($breadcrumb)
            <span style="color:#1e2a45; font-size:14px;">/</span>
            <span style="color:#4a5a7a; font-size:12px;">{{ $breadcrumb }}</span>
            @endisset
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
            @if(session('success'))
            <div style="display:flex; align-items:center; gap:6px; background:#14532d20; border:1px solid #14532d50; border-radius:6px; padding:5px 10px; color:#4ade80; font-size:12px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                {{ session('success') }}
            </div>
            @endif

            <x-ui.badge variant="accent">{{ auth()->user()->getRoleNames()->first() ?? 'user' }}</x-ui.badge>
        </div>
    </header>

    {{-- PAGE --}}
    <main style="flex:1; overflow-y:auto; padding:24px;">
        {{ $slot }}
    </main>
</div>

</body>
</html>
