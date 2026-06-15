@props([
    'name'  => '',
    'type'  => 'text',
    'error' => null,
    'label' => null,
])

<div>
    @if($label)
    <label for="{{ $name }}" style="display:block;color:rgba(255,255,255,0.45);font-size:11px;font-weight:500;margin-bottom:6px;font-family:'Onest',sans-serif;text-transform:uppercase;letter-spacing:0.08em;">
        {{ $label }}
    </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        style="width:100%;padding:7px 12px;background:transparent;border:1px solid {{ $error ? 'rgba(248,113,113,0.5)' : 'rgba(255,255,255,0.18)' }};border-radius:4px;color:rgba(255,255,255,0.75);font-size:13px;font-weight:300;font-family:'Onest',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.15s,box-shadow 0.15s;"
        onfocus="this.style.borderColor='rgba(94,106,210,0.7)';this.style.boxShadow='0 0 0 3px rgba(94,106,210,0.12)'"
        onblur="this.style.borderColor='{{ $error ? 'rgba(248,113,113,0.5)' : 'rgba(255,255,255,0.18)' }}';this.style.boxShadow='none'"
        {{ $attributes }}
    >

    @if($error)
    <p style="color:rgba(248,113,113,0.9);font-size:11px;margin-top:5px;font-family:'Onest',sans-serif;letter-spacing:0.03em;">{{ $error }}</p>
    @endif
</div>
