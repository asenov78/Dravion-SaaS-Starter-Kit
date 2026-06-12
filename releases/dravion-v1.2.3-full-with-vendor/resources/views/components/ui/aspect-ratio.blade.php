@props(['ratio' => '16/9'])

@php
// Convert "16/9" → 56.25%
[$w, $h] = array_map('floatval', explode('/', str_replace(':', '/', $ratio)));
$pct = round(($h / $w) * 100, 4);
@endphp

<div style="position:relative; width:100%; padding-top:{{ $pct }}%; overflow:hidden;" {{ $attributes }}>
    <div style="position:absolute; inset:0;">
        {{ $slot }}
    </div>
</div>
