@props(['variant' => 'default'])

@php
$style = match($variant) {
    'success' => 'background:#14532d20; color:#4ade80; border:1px solid #14532d50;',
    'danger'  => 'background:#7f1d1d20; color:#f87171; border:1px solid #7f1d1d50;',
    'warning' => 'background:#78350f20; color:#fbbf24; border:1px solid #78350f50;',
    'accent'  => 'background:#1e1e2e; color:#5e6ad2; border:1px solid #2a2a35;',
    default   => 'background:#1e1e27; color:#6b6b7b; border:1px solid #2a2a35;',
};
@endphp

<span style="{{ $style }} display:inline-block; padding:2px 8px; border-radius:5px; font-size:11px; font-weight:600; font-family:Inter,system-ui;" {{ $attributes }}>
    {{ $slot }}
</span>
