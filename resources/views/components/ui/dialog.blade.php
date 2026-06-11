@props(['title' => ''])

<div x-data="{ open: false }" style="font-family:Inter,system-ui;" {{ $attributes }}>
    {{-- Trigger --}}
    <div @click="open = true" style="display:inline-block; cursor:pointer;">
        {{ $trigger }}
    </div>

    {{-- Backdrop + Modal --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="open = false"
        style="display:none; position:fixed; inset:0; z-index:50; display:flex; align-items:center; justify-content:center; padding:16px;"
        x-show="open"
    >
        <div @click="open = false" style="position:absolute; inset:0; background:rgba(0,0,0,0.6);"></div>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            style="position:relative; background:#111113; border:1px solid #2a2a35; border-radius:12px; padding:24px; width:100%; max-width:480px; z-index:10;"
        >
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                <h3 style="color:#e2e2e9; font-size:15px; font-weight:600; margin:0;">{{ $title }}</h3>
                <button type="button" @click="open = false" style="background:transparent; border:none; cursor:pointer; color:#6b6b7b; padding:4px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="color:#9b9bab; font-size:13px; line-height:1.6;">{{ $slot }}</div>
        </div>
    </div>
</div>
