@props(['title' => null, 'action' => null])

<div style="background:#1a1a1f; border:1px solid #2a2a35; border-radius:10px; overflow:hidden;" {{ $attributes }}>
    @if($title)
    <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid #2a2a35;">
        <h3 style="color:#e2e2e9; font-size:13px; font-weight:600; margin:0; font-family:Inter,system-ui;">{{ $title }}</h3>
        @if($action ?? false)
        <div>{{ $action }}</div>
        @endif
    </div>
    @endif
    <div style="padding:{{ $title ? '20px' : '20px' }};">
        {{ $slot }}
    </div>
</div>
