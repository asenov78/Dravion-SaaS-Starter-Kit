@props(['title' => ''])

<div x-data="{ open: false }" style="border:1px solid #2a2a35; border-radius:8px; overflow:hidden; font-family:Inter,system-ui;" {{ $attributes }}>
    <button
        type="button"
        @click="open = !open"
        style="width:100%; display:flex; align-items:center; justify-content:space-between; padding:14px 16px; background:transparent; border:none; cursor:pointer; color:#e2e2e9; font-size:13px; font-weight:500; text-align:left;"
    >
        <span>{{ $title }}</span>
        <svg :style="open ? 'transform:rotate(180deg)' : ''" style="width:14px; height:14px; transition:transform 0.2s; flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </button>
    <div x-show="open" x-collapse style="padding:0 16px 14px; color:#9b9bab; font-size:13px; line-height:1.6; border-top:1px solid #2a2a35;">
        {{ $slot }}
    </div>
</div>
