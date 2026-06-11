@props([
    'variant' => 'primary',
    'type'    => 'button',
    'size'    => 'md',
])

@php
$styles = match($variant) {
    'primary' => 'background:#5e6ad2; color:#fff; border:1px solid transparent;',
    'secondary'=> 'background:transparent; color:#e2e2e9; border:1px solid #2a2a35;',
    'danger'  => 'background:transparent; color:#f87171; border:1px solid #7f1d1d50;',
    'ghost'   => 'background:transparent; color:#6b6b7b; border:1px solid transparent;',
    default   => 'background:#5e6ad2; color:#fff; border:1px solid transparent;',
};

$hover = match($variant) {
    'primary'  => 'this.style.background=\'#7b84e0\'',
    'secondary'=> 'this.style.background=\'#1a1a1f\'',
    'danger'   => 'this.style.background=\'#7f1d1d20\'',
    'ghost'    => 'this.style.background=\'#1a1a1f\';this.style.color=\'#e2e2e9\'',
    default    => 'this.style.background=\'#7b84e0\'',
};

$hoverOut = match($variant) {
    'primary'  => 'this.style.background=\'#5e6ad2\'',
    'secondary'=> 'this.style.background=\'transparent\'',
    'danger'   => 'this.style.background=\'transparent\'',
    'ghost'    => 'this.style.background=\'transparent\';this.style.color=\'#6b6b7b\'',
    default    => 'this.style.background=\'#5e6ad2\'',
};

$padding = match($size) {
    'sm' => 'padding:5px 12px; font-size:12px;',
    'lg' => 'padding:10px 20px; font-size:15px;',
    default => 'padding:7px 14px; font-size:13px;',
};
@endphp

<button
    type="{{ $type }}"
    style="{{ $styles }} {{ $padding }} border-radius:7px; font-weight:500; cursor:pointer; font-family:Inter,system-ui; transition:background 120ms; line-height:1.4;"
    onmouseover="{{ $hover }}"
    onmouseout="{{ $hoverOut }}"
    {{ $attributes }}
>{{ $slot }}</button>
