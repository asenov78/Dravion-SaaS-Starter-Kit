@props(['current' => 1, 'total' => 1, 'url' => ''])

@if($total > 1)
<nav style="display:flex; align-items:center; gap:4px; font-family:Inter,system-ui;" {{ $attributes }}>
    @if($current > 1)
    <a href="{{ $url }}?page={{ $current - 1 }}" style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border:1px solid #2a2a35; border-radius:6px; color:#6b6b7b; text-decoration:none; font-size:13px;" onmouseover="this.style.borderColor='#5e6ad2';this.style.color='#e2e2e9'" onmouseout="this.style.borderColor='#2a2a35';this.style.color='#6b6b7b'">‹</a>
    @endif

    @for($p = 1; $p <= $total; $p++)
        @if($p === $current)
        <span style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; background:#5e6ad2; border-radius:6px; color:#fff; font-size:13px; font-weight:600;">{{ $p }}</span>
        @elseif(abs($p - $current) <= 2 || $p === 1 || $p === $total)
        <a href="{{ $url }}?page={{ $p }}" style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border:1px solid #2a2a35; border-radius:6px; color:#6b6b7b; text-decoration:none; font-size:13px;" onmouseover="this.style.borderColor='#5e6ad2';this.style.color='#e2e2e9'" onmouseout="this.style.borderColor='#2a2a35';this.style.color='#6b6b7b'">{{ $p }}</a>
        @elseif(abs($p - $current) === 3)
        <span style="color:#3a3a45; font-size:13px; padding:0 4px;">…</span>
        @endif
    @endfor

    @if($current < $total)
    <a href="{{ $url }}?page={{ $current + 1 }}" style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border:1px solid #2a2a35; border-radius:6px; color:#6b6b7b; text-decoration:none; font-size:13px;" onmouseover="this.style.borderColor='#5e6ad2';this.style.color='#e2e2e9'" onmouseout="this.style.borderColor='#2a2a35';this.style.color='#6b6b7b'">›</a>
    @endif
</nav>
@endif
