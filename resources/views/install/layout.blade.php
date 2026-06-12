<!DOCTYPE html>
<html lang="en" style="height:100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Dravion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #1e2a45; border-radius: 3px; }
    </style>
</head>
<body style="background:#060d1a; font-family:Inter,system-ui,sans-serif; min-height:100%; display:flex; align-items:center; justify-content:center; padding:24px; position:relative;">

<x-ui.net-bg />

<div style="width:100%; max-width:560px; position:relative; z-index:1;">

    {{-- Logo --}}
    <div style="display:flex; align-items:center; justify-content:center; gap:10px; margin-bottom:32px;">
        <div style="width:36px; height:36px; background:linear-gradient(135deg,#5e6ad2,#818cf8); border-radius:10px; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 12px rgba(94,106,210,0.4);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
        </div>
        <span style="color:#e2e2e9; font-size:18px; font-weight:700; letter-spacing:-0.02em;">Dravion Installer</span>
    </div>

    {{-- Step progress --}}
    @php
    $stepLabels = ['requirements' => 'Requirements', 'database' => 'Database', 'admin' => 'Admin', 'license' => 'License', 'finish' => 'Finish'];
    $currentIdx = array_search($current, $steps);
    @endphp
    <div style="display:flex; align-items:center; gap:0; margin-bottom:28px;">
        @foreach($steps as $i => $s)
        @php $idx = $i; $done = $idx < $currentIdx; $active = $s === $current; @endphp
        <div style="display:flex; align-items:center; flex:1; min-width:0;">
            <div style="display:flex; flex-direction:column; align-items:center; gap:4px; flex-shrink:0;">
                <div style="width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700;
                    {{ $done ? 'background:rgba(94,106,210,0.3); color:#818cf8; border:1.5px solid #5e6ad2;' : ($active ? 'background:#5e6ad2; color:#fff; box-shadow:0 0 12px rgba(94,106,210,0.5);' : 'background:rgba(255,255,255,0.05); color:#2a3a55; border:1.5px solid rgba(255,255,255,0.08);') }}">
                    @if($done)
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    @else
                    {{ $idx + 1 }}
                    @endif
                </div>
                <span style="font-size:10px; font-weight:500; white-space:nowrap; {{ $active ? 'color:#a5b0f5;' : ($done ? 'color:#4a5a7a;' : 'color:#2a3a55;') }}">{{ $stepLabels[$s] }}</span>
            </div>
            @if($i < count($steps) - 1)
            <div style="flex:1; height:1px; margin:0 4px; margin-bottom:16px; {{ $done ? 'background:#5e6ad2;' : 'background:rgba(255,255,255,0.06);' }}"></div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Card --}}
    <div style="background:rgba(8,16,36,0.8); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.07); border-radius:14px; padding:28px;">
        {{ $slot }}
    </div>

    <p style="text-align:center; margin-top:16px; color:#2a3a55; font-size:11px;">
        Dravion v{{ config('dravion.version') }} &nbsp;·&nbsp; © {{ date('Y') }}
    </p>
</div>

</body>
</html>
