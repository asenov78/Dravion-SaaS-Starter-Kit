@props([
    'name'     => '',
    'options'  => [],
    'selected' => null,
    'multiple' => false,
])

<div
    x-data="{ selected: {{ $multiple ? '[]' : json_encode($selected) }} }"
    style="display:inline-flex; gap:2px; background:#0a0a0b; border:1px solid #2a2a35; border-radius:8px; padding:3px; font-family:Inter,system-ui;"
    {{ $attributes }}
>
    @foreach($options as $value => $label)
    <button
        type="button"
        @if($multiple)
            @click="selected.includes('{{ $value }}') ? selected = selected.filter(v => v !== '{{ $value }}') : selected.push('{{ $value }}')"
            :style="selected.includes('{{ $value }}') ? 'background:#5e6ad2; color:#fff;' : 'background:transparent; color:#6b6b7b;'"
        @else
            @click="selected = '{{ $value }}'"
            :style="selected === '{{ $value }}' ? 'background:#5e6ad2; color:#fff;' : 'background:transparent; color:#6b6b7b;'"
        @endif
        style="padding:5px 12px; border:none; border-radius:6px; cursor:pointer; font-size:12px; font-weight:500; transition:all 0.15s;"
    >{{ $label }}</button>
    @endforeach

    @if($multiple)
        <template x-for="v in selected" :key="v">
            <input type="hidden" name="{{ $name }}[]" :value="v">
        </template>
    @else
        <input type="hidden" name="{{ $name }}" :value="selected">
    @endif
</div>
