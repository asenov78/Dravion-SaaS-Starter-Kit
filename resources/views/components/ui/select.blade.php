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
    <label for="{{ $name }}" style="display:block;color:rgba(255,255,255,0.45);font-size:11px;font-weight:500;margin-bottom:6px;font-family:'Onest',sans-serif;text-transform:uppercase;letter-spacing:0.08em;">{{ $label }}</label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        style="width:100%;padding:7px 12px;background:rgba(18,28,44,0.8);border:1px solid {{ $error ? 'rgba(248,113,113,0.5)' : 'rgba(255,255,255,0.18)' }};border-radius:4px;color:rgba(255,255,255,0.75);font-size:13px;font-weight:300;font-family:'Onest',sans-serif;outline:none;box-sizing:border-box;cursor:pointer;transition:border-color 0.15s,box-shadow 0.15s;appearance:auto;"
        onfocus="this.style.borderColor='rgba(94,106,210,0.7)';this.style.boxShadow='0 0 0 3px rgba(94,106,210,0.12)'"
        onblur="this.style.borderColor='{{ $error ? 'rgba(248,113,113,0.5)' : 'rgba(255,255,255,0.18)' }}';this.style.boxShadow='none'"
        {{ $attributes }}
    >
        @if($placeholder)
        <option value="" disabled @if(!$selected) selected @endif style="background:#121c2c;">{{ $placeholder }}</option>
        @endif

        @foreach($options as $value => $optLabel)
        <option value="{{ $value }}" @if((string)$selected === (string)$value) selected @endif style="background:#121c2c;">{{ $optLabel }}</option>
        @endforeach
    </select>

    @if($error)
    <p style="color:rgba(248,113,113,0.9);font-size:11px;margin-top:5px;font-family:'Onest',sans-serif;letter-spacing:0.03em;">{{ $error }}</p>
    @endif
</div>
