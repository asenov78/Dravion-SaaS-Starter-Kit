@props([
    'name'    => '',
    'label'   => null,
    'checked' => false,
    'value'   => '1',
])

<div style="display:flex; align-items:center; gap:10px; font-family:Inter,system-ui;" {{ $attributes }}>
    <div
        x-data="{ on: {{ $checked ? 'true' : 'false' }} }"
        @click="on = !on"
        :style="on ? 'background:#5e6ad2;' : 'background:#2a2a35;'"
        style="width:36px; height:20px; border-radius:999px; cursor:pointer; position:relative; transition:background 0.2s; flex-shrink:0;"
    >
        <div
            :style="on ? 'transform:translateX(16px);' : 'transform:translateX(2px);'"
            style="width:16px; height:16px; background:#fff; border-radius:50%; position:absolute; top:2px; transition:transform 0.2s;"
        ></div>
        <input type="hidden" name="{{ $name }}" :value="on ? '{{ $value }}' : '0'">
    </div>
    @if($label)
    <span style="color:#c2c2ce; font-size:13px; cursor:pointer;" @click="on = !on">{{ $label }}</span>
    @endif
</div>
