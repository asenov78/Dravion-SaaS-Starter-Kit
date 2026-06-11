@props(['label' => '', 'value' => 0, 'color' => '#5e6ad2'])

<div style="background:#1a1a1f; border:1px solid #2a2a35; border-radius:10px; padding:20px 24px;">
    <p style="color:#6b6b7b; font-size:12px; font-weight:500; margin:0 0 8px; font-family:Inter,system-ui;">{{ $label }}</p>
    <p style="color:{{ $color }}; font-size:28px; font-weight:700; margin:0; font-family:Inter,system-ui;">{{ $value }}</p>
    @if($slot->isNotEmpty())
    <p style="color:#6b6b7b; font-size:11px; margin:6px 0 0; font-family:Inter,system-ui;">{{ $slot }}</p>
    @endif
</div>
