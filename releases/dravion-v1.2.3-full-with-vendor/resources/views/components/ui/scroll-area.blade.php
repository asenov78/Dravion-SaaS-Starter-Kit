@props(['height' => '300px', 'width' => '100%'])

<div
    style="height:{{ $height }}; width:{{ $width }}; overflow-y:auto; overflow-x:hidden; scrollbar-width:thin; scrollbar-color:#2a2a35 transparent;"
    {{ $attributes }}
>
    {{ $slot }}
</div>

<style>
/* Webkit scrollbar */
[style*="overflow-y:auto"]::-webkit-scrollbar { width: 6px; }
[style*="overflow-y:auto"]::-webkit-scrollbar-track { background: transparent; }
[style*="overflow-y:auto"]::-webkit-scrollbar-thumb { background: #2a2a35; border-radius: 3px; }
[style*="overflow-y:auto"]::-webkit-scrollbar-thumb:hover { background: #3a3a45; }
</style>
