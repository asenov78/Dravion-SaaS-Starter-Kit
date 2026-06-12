@props(['for' => null])

<label
    @if($for) for="{{ $for }}" @endif
    style="display:block; color:#6b6b7b; font-size:12px; font-weight:500; margin-bottom:5px; font-family:Inter,system-ui;"
    {{ $attributes }}
>{{ $slot }}</label>
