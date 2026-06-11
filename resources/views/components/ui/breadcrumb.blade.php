@props(['items' => []])

<nav aria-label="breadcrumb" style="font-family:Inter,system-ui;">
    <ol style="display:flex; align-items:center; gap:6px; list-style:none; margin:0; padding:0; font-size:12px;">
        @foreach($items as $i => $item)
            @if($i > 0)
                <li style="color:#3a3a45;">/</li>
            @endif
            <li>
                @if(isset($item['href']) && $i < count($items) - 1)
                    <a href="{{ $item['href'] }}" style="color:#6b6b7b; text-decoration:none;" onmouseover="this.style.color='#e2e2e9'" onmouseout="this.style.color='#6b6b7b'">{{ $item['label'] }}</a>
                @else
                    <span style="color:#e2e2e9;">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
