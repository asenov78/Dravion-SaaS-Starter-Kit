@props(['title' => '', 'side' => 'bottom'])

@php
$isBottom = $side === 'bottom' || $side === 'top';
$enterStart = match($side) {
    'top'   => 'transform:translateY(-100%)',
    'left'  => 'transform:translateX(-100%)',
    'right' => 'transform:translateX(100%)',
    default => 'transform:translateY(100%)',
};
$position = match($side) {
    'top'   => 'top:0; left:0; right:0;',
    'left'  => 'left:0; top:0; bottom:0; width:320px;',
    'right' => 'right:0; top:0; bottom:0; width:320px;',
    default => 'bottom:0; left:0; right:0;',
};
$radius = match($side) {
    'top'   => 'border-radius:0 0 12px 12px;',
    'left'  => 'border-radius:0 12px 12px 0;',
    'right' => 'border-radius:12px 0 0 12px;',
    default => 'border-radius:12px 12px 0 0;',
};
@endphp

<div x-data="{ open: false }" style="font-family:Inter,system-ui;" {{ $attributes }}>
    <div @click="open = true" style="display:inline-block; cursor:pointer;">
        {{ $trigger }}
    </div>

    <div x-show="open" @keydown.escape.window="open = false" style="position:fixed; inset:0; z-index:50;" x-cloak>
        <div @click="open = false" x-show="open"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            style="position:absolute; inset:0; background:rgba(0,0,0,0.6);"></div>

        <div x-show="open"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="{{ $enterStart }}" x-transition:enter-end="transform:translate(0)"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="transform:translate(0)" x-transition:leave-end="{{ $enterStart }}"
            style="position:absolute; {{ $position }} background:#111113; border:1px solid #2a2a35; {{ $radius }} {{ $isBottom ? 'max-height:80vh;' : '' }} display:flex; flex-direction:column;"
        >
            <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #2a2a35;">
                <h3 style="color:#e2e2e9; font-size:14px; font-weight:600; margin:0;">{{ $title }}</h3>
                <button type="button" @click="open = false" style="background:transparent; border:none; cursor:pointer; color:#6b6b7b;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="flex:1; overflow-y:auto; padding:20px;">{{ $slot }}</div>
        </div>
    </div>
</div>
