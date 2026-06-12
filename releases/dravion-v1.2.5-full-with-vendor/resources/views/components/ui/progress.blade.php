@props(['value' => 0, 'color' => '#5e6ad2'])

@php $pct = min(100, max(0, (int) $value)); @endphp

<div style="width:100%; background:#2a2a35; border-radius:999px; height:6px; overflow:hidden;" {{ $attributes }}>
    <div style="width:{{ $pct }}%; height:100%; background:{{ $color }}; border-radius:999px; transition:width 0.3s ease;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<span style="display:none;">{{ $pct }}%</span>
