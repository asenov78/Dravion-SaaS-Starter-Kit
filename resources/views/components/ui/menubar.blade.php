@props(['menus' => []])

<div
    style="display:flex; align-items:center; gap:2px; background:#111113; border:1px solid #2a2a35; border-radius:8px; padding:3px; font-family:Inter,system-ui;"
    {{ $attributes }}
>
    @foreach($menus as $idx => $menu)
    <div x-data="{ open: false }" style="position:relative;">
        <button
            type="button"
            @click="open = !open"
            @click.outside="open = false"
            :style="open ? 'background:#1a1a1f; color:#e2e2e9;' : 'color:#9b9bab;'"
            style="padding:5px 10px; background:transparent; border:none; border-radius:6px; cursor:pointer; font-size:12px; font-weight:500; transition:all 0.15s;"
        >{{ $menu['label'] }}</button>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            style="position:absolute; top:calc(100% + 4px); left:0; min-width:160px; background:#111113; border:1px solid #2a2a35; border-radius:8px; padding:4px; z-index:50; box-shadow:0 8px 24px rgba(0,0,0,0.4);"
        >
            @foreach($menu['items'] as $item)
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
    @endforeach
</div>
