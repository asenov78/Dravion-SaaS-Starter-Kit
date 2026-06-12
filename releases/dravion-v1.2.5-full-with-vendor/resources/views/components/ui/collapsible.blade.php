@props(['open' => false])

<div x-data="{ open: {{ $open ? 'true' : 'false' }} }" style="font-family:Inter,system-ui;" {{ $attributes }}>
    <div @click="open = !open" style="cursor:pointer;">
        {{ $trigger }}
    </div>
    <div x-show="open" x-collapse>
        {{ $slot }}
    </div>
</div>
