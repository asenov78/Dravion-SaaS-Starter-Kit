@props(['size' => 16, 'color' => '#5e6ad2'])

<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="animation:spin 0.8s linear infinite;" {{ $attributes }}>
    <circle cx="12" cy="12" r="10" stroke="{{ $color }}" stroke-width="2" stroke-opacity="0.25"/>
    <path d="M12 2a10 10 0 0 1 10 10" stroke="{{ $color }}" stroke-width="2" stroke-linecap="round"/>
</svg>

<style>
@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
</style>
