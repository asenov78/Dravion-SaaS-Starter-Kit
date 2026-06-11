@props(['name' => '', 'src' => null, 'size' => 32])

@php
$initials = collect(explode(' ', trim($name)))
    ->filter()
    ->take(2)
    ->map(fn($w) => strtoupper($w[0]))
    ->implode('');
$fontSize = round($size * 0.38);
@endphp

<div style="width:{{ $size }}px; height:{{ $size }}px; border-radius:50%; overflow:hidden; display:inline-flex; align-items:center; justify-content:center; background:#2a2a35; flex-shrink:0;" {{ $attributes }}>
    @if($src)
        <img src="{{ $src }}" alt="{{ $name }}" style="width:100%; height:100%; object-fit:cover;">
    @else
        <span style="color:#e2e2e9; font-size:{{ $fontSize }}px; font-weight:600; font-family:Inter,system-ui; line-height:1;">{{ $initials }}</span>
    @endif
</div>
