@props([
    'name'    => '',
    'pressed' => false,
    'value'   => '1',
])

<button
    type="button"
    x-data="{ pressed: {{ $pressed ? 'true' : 'false' }} }"
    @click="pressed = !pressed"
    :style="pressed ? 'background:#5e6ad225; border-color:#5e6ad2; color:#5e6ad2;' : 'background:transparent; border-color:#2a2a35; color:#6b6b7b;'"
    style="display:inline-flex; align-items:center; justify-content:center; padding:6px 10px; border:1px solid; border-radius:6px; cursor:pointer; font-size:13px; font-weight:500; font-family:Inter,system-ui; transition:all 0.15s;"
    {{ $attributes }}
>{{ $slot }}</button>
