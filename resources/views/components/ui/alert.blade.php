@props(['variant' => 'error'])

@php
$style = match($variant) {
    'success' => 'background:#14532d20; border:1px solid #14532d50; color:#4ade80;',
    'warning' => 'background:#78350f20; border:1px solid #78350f50; color:#fbbf24;',
    'info'    => 'background:#1e3a5f20; border:1px solid #1e3a5f50; color:#60a5fa;',
    default   => 'background:#7f1d1d20; border:1px solid #7f1d1d50; color:#f87171;',
};
@endphp

<div style="{{ $style }} padding:10px 14px; border-radius:8px; font-size:13px; font-family:Inter,system-ui; line-height:1.5;" {{ $attributes }}>
    {{ $slot }}
</div>
