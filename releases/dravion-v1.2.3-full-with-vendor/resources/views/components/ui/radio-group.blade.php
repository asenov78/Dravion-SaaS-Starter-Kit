@props([
    'name'     => '',
    'options'  => [],
    'selected' => null,
    'label'    => null,
])

<div>
    @if($label)
    <p style="color:#6b6b7b; font-size:12px; font-weight:500; margin:0 0 8px; font-family:Inter,system-ui;">{{ $label }}</p>
    @endif

    <div style="display:flex; flex-direction:column; gap:8px;">
        @foreach($options as $value => $optLabel)
        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-family:Inter,system-ui;">
            <input
                type="radio"
                name="{{ $name }}"
                value="{{ $value }}"
                @if((string)$selected === (string)$value) checked @endif
                style="accent-color:#5e6ad2; width:15px; height:15px; cursor:pointer;"
            >
            <span style="color:#c2c2ce; font-size:13px;">{{ $optLabel }}</span>
        </label>
        @endforeach
    </div>
</div>
