@props(['tabs' => [], 'default' => 0])

<div x-data="{ active: {{ $default }} }" style="font-family:Inter,system-ui;" {{ $attributes }}>
    <div style="display:flex; gap:2px; border-bottom:1px solid #2a2a35; margin-bottom:16px;">
        @foreach($tabs as $i => $tab)
        <button
            type="button"
            @click="active = {{ $i }}"
            :style="active === {{ $i }} ? 'color:#e2e2e9; border-bottom:2px solid #5e6ad2; margin-bottom:-1px;' : 'color:#6b6b7b; border-bottom:2px solid transparent; margin-bottom:-1px;'"
            style="padding:8px 14px; background:transparent; border:none; border-left:none; border-right:none; border-top:none; cursor:pointer; font-size:13px; font-weight:500; transition:color 0.15s;"
        >{{ $tab['label'] }}</button>
        @endforeach
    </div>
    <div>{{ $slot }}</div>
</div>
