@props(['items' => [], 'active' => null, 'orientation' => 'horizontal'])

@php
$isVertical = $orientation === 'vertical';
$currentPath = $active ?? request()->path();
@endphp

<nav style="font-family:Inter,system-ui;" {{ $attributes }}>
    <ul style="display:flex; flex-direction:{{ $isVertical ? 'column' : 'row' }}; gap:{{ $isVertical ? '2px' : '4px' }}; list-style:none; margin:0; padding:0;">
        @foreach($items as $item)
        @php
            $href = $item['href'] ?? '#';
            $isActive = $active ? ($active === $href) : request()->is(ltrim($href, '/'));
            $hasChildren = !empty($item['children']);
        @endphp
        <li @if($hasChildren) x-data="{ open: false }" @endif style="position:relative;">
            @if($hasChildren)
            <button
                type="button"
                @click="open = !open"
                @click.outside="open = false"
                style="display:flex; align-items:center; gap:5px; padding:7px 12px; background:{{ $isActive ? '#5e6ad220' : 'transparent' }}; border:none; border-radius:7px; color:{{ $isActive ? '#5e6ad2' : '#9b9bab' }}; font-size:13px; font-weight:{{ $isActive ? '500' : '400' }}; cursor:pointer; text-decoration:none; transition:all 0.15s; white-space:nowrap;"
            >
                {{ $item['label'] }}
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform 0.2s;"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                style="position:absolute; top:calc(100% + 4px); left:0; min-width:180px; background:#111113; border:1px solid #2a2a35; border-radius:8px; padding:4px; z-index:50; box-shadow:0 8px 24px rgba(0,0,0,0.4);"
            >
                @foreach($item['children'] as $child)
                <a href="{{ $child['href'] ?? '#' }}" style="display:block; padding:7px 10px; color:#c2c2ce; font-size:12px; text-decoration:none; border-radius:5px;" onmouseover="this.style.background='#1a1a1f'" onmouseout="this.style.background='transparent'">{{ $child['label'] }}</a>
                @endforeach
            </div>
            @else
            <a
                href="{{ $href }}"
                style="display:block; padding:7px 12px; background:{{ $isActive ? '#5e6ad220' : 'transparent' }}; border-radius:7px; color:{{ $isActive ? '#5e6ad2' : '#9b9bab' }}; font-size:13px; font-weight:{{ $isActive ? '500' : '400' }}; text-decoration:none; transition:all 0.15s; white-space:nowrap;"
                onmouseover="if(!{{ $isActive ? 'true' : 'false' }}) { this.style.background='#1a1a1f'; this.style.color='#e2e2e9'; }"
                onmouseout="if(!{{ $isActive ? 'true' : 'false' }}) { this.style.background='transparent'; this.style.color='#9b9bab'; }"
            >{{ $item['label'] }}</a>
            @endif
        </li>
        @endforeach
    </ul>
</nav>
