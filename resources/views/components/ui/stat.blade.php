@props(['label' => '', 'value' => 0, 'color' => '#5e6ad2'])

<div style="position:relative;background:rgba(8,16,36,0.6);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:20px 20px 16px;overflow:visible;">
    <span class="hud-corner tl"></span>
    <span class="hud-corner tr"></span>
    <span class="hud-corner bl"></span>
    <span class="hud-corner br"></span>
    <p style="color:rgba(255,255,255,0.35);font-size:10px;font-weight:600;margin:0 0 10px;font-family:'Onest',sans-serif;text-transform:uppercase;letter-spacing:0.1em;">{{ $label }}</p>
    <p style="color:{{ $color }};font-size:30px;font-weight:600;margin:0;font-family:'Onest',sans-serif;letter-spacing:-0.02em;">{{ $value }}</p>
    @if($slot->isNotEmpty())
    <p style="color:rgba(255,255,255,0.25);font-size:11px;margin:6px 0 0;font-family:'Onest',sans-serif;">{{ $slot }}</p>
    @endif
</div>
