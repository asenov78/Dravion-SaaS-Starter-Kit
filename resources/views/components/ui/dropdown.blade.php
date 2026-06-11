@props(['items' => [], 'align' => 'left'])

<div x-data="{ open: false }" style="position:relative; display:inline-block; font-family:Inter,system-ui;" {{ $attributes }}>
    <div @click="open = !open" @click.outside="open = false" style="cursor:pointer;">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="position:absolute; top:calc(100% + 6px); {{ $align === 'right' ? 'right:0;' : 'left:0;' }} min-width:160px; background:#111113; border:1px solid #2a2a35; border-radius:8px; padding:4px; z-index:50; box-shadow:0 8px 24px rgba(0,0,0,0.4);"
    >
        @foreach($items as $item)
            @if(isset($item['separator']))
            <div style="border-top:1px solid #2a2a35; margin:4px 0;"></div>
            @else
            <a
                href="{{ $item['href'] ?? '#' }}"
                @if(isset($item['action'])) @click.prevent="{{ $item['action'] }}" @endif
                style="display:block; padding:7px 10px; color:{{ isset($item['danger']) ? '#f87171' : '#c2c2ce' }}; font-size:12px; text-decoration:none; border-radius:5px; transition:background 0.1s;"
                onmouseover="this.style.background='#1a1a1f'"
                onmouseout="this.style.background='transparent'"
            >{{ $item['label'] }}</a>
            @endif
        @endforeach
        {{ $slot }}
    </div>
</div>
