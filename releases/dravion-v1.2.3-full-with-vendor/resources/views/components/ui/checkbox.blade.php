@props([
    'name'    => '',
    'label'   => null,
    'checked' => false,
    'value'   => '1',
])

<div style="display:flex; align-items:center; gap:8px; font-family:Inter,system-ui;">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value }}"
        @if($checked) checked @endif
        style="width:15px; height:15px; accent-color:#5e6ad2; cursor:pointer; border-radius:4px;"
        {{ $attributes }}
    >
    @if($label)
    <label for="{{ $name }}" style="color:#c2c2ce; font-size:13px; cursor:pointer;">{{ $label }}</label>
    @endif
</div>
