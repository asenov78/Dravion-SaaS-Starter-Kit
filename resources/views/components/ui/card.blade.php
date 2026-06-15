@props(['title' => null, 'action' => null])

<div style="position:relative;background:rgba(8,16,36,0.6);border:1px solid rgba(255,255,255,0.08);border-radius:12px;overflow:visible;" {{ $attributes }}>
    {{-- HUD corner brackets --}}
    <span class="hud-corner tl"></span>
    <span class="hud-corner tr"></span>
    <span class="hud-corner bl"></span>
    <span class="hud-corner br"></span>

    @if($title)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid rgba(255,255,255,0.06);">
        <h3 style="color:rgba(255,255,255,0.5);font-size:10.5px;font-weight:600;margin:0;font-family:'Onest',sans-serif;text-transform:uppercase;letter-spacing:0.08em;">{{ $title }}</h3>
        @if($action ?? false)
        <div>{{ $action }}</div>
        @endif
    </div>
    @endif

    <div style="padding:16px;">
        {{ $slot }}
    </div>
</div>
