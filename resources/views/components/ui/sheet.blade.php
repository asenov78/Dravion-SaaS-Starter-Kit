@props(['title' => '', 'side' => 'right', 'width' => '400px'])

<div x-data="{ open: false }" style="font-family:Inter,system-ui;" {{ $attributes }}>
    <div @click="open = true" style="display:inline-block; cursor:pointer;">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        @keydown.escape.window="open = false"
        style="position:fixed; inset:0; z-index:50;"
        x-cloak
    >
        {{-- Backdrop --}}
        <div
            @click="open = false"
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="position:absolute; inset:0; background:rgba(0,0,0,0.6);"
        ></div>

        {{-- Panel --}}
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="{{ $side === 'right' ? 'transform:translateX(100%)' : 'transform:translateX(-100%)' }}"
            x-transition:enter-end="transform:translateX(0)"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="transform:translateX(0)"
            x-transition:leave-end="{{ $side === 'right' ? 'transform:translateX(100%)' : 'transform:translateX(-100%)' }}"
            style="position:absolute; {{ $side === 'right' ? 'right:0;' : 'left:0;' }} top:0; bottom:0; width:{{ $width }}; max-width:100vw; background:#111113; border-{{ $side === 'right' ? 'left' : 'right' }}:1px solid #2a2a35; display:flex; flex-direction:column;"
        >
            <div style="display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid #2a2a35;">
                <h3 style="color:#e2e2e9; font-size:15px; font-weight:600; margin:0;">{{ $title }}</h3>
                <button type="button" @click="open = false" style="background:transparent; border:none; cursor:pointer; color:#6b6b7b;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="flex:1; overflow-y:auto; padding:24px;">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
