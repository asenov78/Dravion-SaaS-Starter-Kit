@props([
    'variant' => 'primary',
    'type'    => 'button',
    'size'    => 'md',
    'tag'     => 'button',
    'href'    => null,
])

@php
$styles = match($variant) {
    'primary'  => 'background:linear-gradient(135deg,#5e6ad2,#818cf8);color:#fff;border:1px solid transparent;',
    'secondary'=> 'background:transparent;color:rgba(255,255,255,0.55);border:1px solid rgba(255,255,255,0.15);',
    'danger'   => 'background:transparent;color:rgba(248,113,113,0.8);border:1px solid rgba(248,113,113,0.25);',
    'ghost'    => 'background:transparent;color:rgba(255,255,255,0.4);border:1px solid transparent;',
    default    => 'background:linear-gradient(135deg,#5e6ad2,#818cf8);color:#fff;border:1px solid transparent;',
};

$hover = match($variant) {
    'primary'  => "this.style.opacity='0.85'",
    'secondary'=> "this.style.borderColor='rgba(255,255,255,0.28)';this.style.color='rgba(255,255,255,0.8)'",
    'danger'   => "this.style.background='rgba(248,113,113,0.1)';this.style.borderColor='rgba(248,113,113,0.4)'",
    'ghost'    => "this.style.color='rgba(255,255,255,0.7)'",
    default    => "this.style.opacity='0.85'",
};

$hoverOut = match($variant) {
    'primary'  => "this.style.opacity='1'",
    'secondary'=> "this.style.borderColor='rgba(255,255,255,0.15)';this.style.color='rgba(255,255,255,0.55)'",
    'danger'   => "this.style.background='transparent';this.style.borderColor='rgba(248,113,113,0.25)'",
    'ghost'    => "this.style.color='rgba(255,255,255,0.4)'",
    default    => "this.style.opacity='1'",
};

$padding = match($size) {
    'sm'  => 'padding:4px 12px;font-size:11px;',
    'lg'  => 'padding:10px 22px;font-size:14px;',
    default => 'padding:7px 16px;font-size:12px;',
};

$tag  = $href ? 'a' : $tag;
$base = "{$styles} {$padding} border-radius:4px;font-weight:500;cursor:pointer;font-family:'Onest',sans-serif;letter-spacing:0.04em;transition:all 0.12s;line-height:1.4;text-decoration:none;display:inline-flex;align-items:center;gap:6px;";
@endphp

@if($tag === 'a')
<a href="{{ $href }}" style="{{ $base }}" onmouseover="{{ $hover }}" onmouseout="{{ $hoverOut }}" {{ $attributes }}>{{ $slot }}</a>
@else
<button type="{{ $type }}" style="{{ $base }}" onmouseover="{{ $hover }}" onmouseout="{{ $hoverOut }}" {{ $attributes }}>{{ $slot }}</button>
@endif
