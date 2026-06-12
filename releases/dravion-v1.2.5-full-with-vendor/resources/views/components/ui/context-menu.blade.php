@props(['items' => []])

<div
    x-data="{ open: false, x: 0, y: 0 }"
    @contextmenu.prevent="open = true; x = $event.clientX; y = $event.clientY"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    style="font-family:Inter,system-ui; display:inline-block;"
    {{ $attributes }}
>
    {{ $slot }}

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        :style="`position:fixed; top:${y}px; left:${x}px; z-index:9999;`"
        style="min-width:160px; background:#111113; border:1px solid #2a2a35; border-radius:8px; padding:4px; box-shadow:0 8px 24px rgba(0,0,0,0.4);"
    >
        @foreach($items as $item)
            @if(isset($item['separator']))
            <div style="border-top:1px solid #2a2a35; margin:4px 0;"></div>
            @else
            <a
                href="{{ $item['href'] ?? '#' }}"
                @click="open = false"
                style="display:flex; align-items:center; justify-content:space-between; padding:7px 10px; color:{{ isset($item['danger']) ? '#f87171' : '#c2c2ce' }}; font-size:12px; text-decoration:none; border-radius:5px; transition:background 0.1s;"
                onmouseover="this.style.background='#1a1a1f'"
                onmouseout="this.style.background='transparent'"
            >
                <span>{{ $item['label'] }}</span>
                @if(isset($item['shortcut']))
                <span style="color:#3a3a45; font-size:11px; font-family:ui-monospace,monospace;">{{ $item['shortcut'] }}</span>
                @endif
            </a>
            @endif
        @endforeach
    </div>
</div>
