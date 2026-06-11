@props(['message' => '', 'variant' => 'default', 'duration' => 4000])

@php
$style = match($variant) {
    'success' => 'border-color:#14532d50; background:#14532d20; color:#4ade80;',
    'warning' => 'border-color:#78350f50; background:#78350f20; color:#fbbf24;',
    'info'    => 'border-color:#1e3a5f50; background:#1e3a5f20; color:#60a5fa;',
    default   => 'border-color:#2a2a35; background:#1a1a1f; color:#e2e2e9;',
};
$icon = match($variant) {
    'success' => '<path d="M20 6 9 17l-5-5"/>',
    'warning' => '<path d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>',
    'info'    => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4m0-4h.01"/>',
    default   => '<circle cx="12" cy="12" r="10"/><path d="m15 9-6 6m0-6 6 6"/>',
};
@endphp

<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, {{ $duration }})"
    x-show="show"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="{{ $style }} border:1px solid; border-radius:8px; padding:12px 16px; display:flex; align-items:center; gap:10px; font-family:Inter,system-ui; font-size:13px; min-width:280px; max-width:400px; box-shadow:0 8px 24px rgba(0,0,0,0.4);"
    {{ $attributes }}
>
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">{!! $icon !!}</svg>
    <span>{{ $message }}</span>
    <button type="button" @click="show = false" style="margin-left:auto; background:transparent; border:none; cursor:pointer; color:inherit; opacity:0.5; padding:0;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
    </button>
</div>
