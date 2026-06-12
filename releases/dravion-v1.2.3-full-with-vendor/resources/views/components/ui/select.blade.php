@props([
    'name'        => '',
    'options'     => [],
    'selected'    => null,
    'placeholder' => null,
    'label'       => null,
    'error'       => null,
])

<div>
    @if($label)
    <label for="{{ $name }}" style="display:block; color:#6b6b7b; font-size:12px; font-weight:500; margin-bottom:5px; font-family:Inter,system-ui;">{{ $label }}</label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        style="width:100%; padding:8px 12px; background:#0a0a0b; border:1px solid {{ $error ? '#7f1d1d' : '#2a2a35' }}; border-radius:7px; color:#e2e2e9; font-size:13px; outline:none; box-sizing:border-box; font-family:Inter,system-ui; cursor:pointer;"
        {{ $attributes }}
    >
        @if($placeholder)
        <option value="" disabled @if(!$selected) selected @endif>{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $optLabel)
        <option value="{{ $value }}" @if((string)$selected === (string)$value) selected @endif>{{ $optLabel }}</option>
        @endforeach
    </select>

    @if($error)
    <p style="color:#f87171; font-size:11px; margin-top:4px; font-family:Inter,system-ui;">{{ $error }}</p>
    @endif
</div>
