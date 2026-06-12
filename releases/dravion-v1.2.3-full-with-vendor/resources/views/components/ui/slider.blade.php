@props([
    'name'  => '',
    'value' => 50,
    'min'   => 0,
    'max'   => 100,
    'step'  => 1,
    'label' => null,
])

<div style="font-family:Inter,system-ui;" {{ $attributes }}>
    @if($label)
    <label for="{{ $name }}" style="display:block; color:#6b6b7b; font-size:12px; font-weight:500; margin-bottom:8px;">{{ $label }}</label>
    @endif

    <input
        type="range"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        min="{{ $min }}"
        max="{{ $max }}"
        step="{{ $step }}"
        style="width:100%; accent-color:#5e6ad2; cursor:pointer; height:4px;"
    >
</div>
