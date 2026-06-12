@props(['text' => '', 'position' => 'top'])

<div x-data="{ show: false }" style="position:relative; display:inline-flex; font-family:Inter,system-ui;" {{ $attributes }}>
    <div @mouseenter="show = true" @mouseleave="show = false">
        {{ $slot }}
    </div>

    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        style="position:absolute; {{ $position === 'bottom' ? 'top:calc(100% + 6px);' : 'bottom:calc(100% + 6px);' }} left:50%; transform:translateX(-50%); white-space:nowrap; background:#1a1a1f; border:1px solid #2a2a35; border-radius:6px; padding:5px 10px; color:#e2e2e9; font-size:11px; pointer-events:none; z-index:50;"
    >{{ $text }}</div>
</div>
