@props(['align' => 'left', 'width' => '240px'])

<div x-data="{ open: false }" style="position:relative; display:inline-block; font-family:Inter,system-ui;" {{ $attributes }}>
    <div @click="open = !open" @click.outside="open = false" style="cursor:pointer;">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="position:absolute; top:calc(100% + 8px); {{ $align === 'right' ? 'right:0;' : 'left:0;' }} width:{{ $width }}; background:#111113; border:1px solid #2a2a35; border-radius:10px; padding:16px; z-index:50; box-shadow:0 8px 24px rgba(0,0,0,0.4);"
    >
        {{ $slot }}
    </div>
</div>
