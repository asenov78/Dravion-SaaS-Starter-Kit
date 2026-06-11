@props([
    'title'       => '',
    'description' => '',
    'confirm'     => 'Confirm',
    'cancel'      => 'Cancel',
    'action'      => null,
    'variant'     => 'danger',
])

<div x-data="{ open: false }" style="font-family:Inter,system-ui;" {{ $attributes }}>
    <div @click="open = true" style="display:inline-block; cursor:pointer;">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        @keydown.escape.window="open = false"
        style="position:fixed; inset:0; z-index:50; display:flex; align-items:center; justify-content:center; padding:16px;"
        x-cloak
    >
        <div @click="open = false"
            x-show="open"
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            style="position:absolute; inset:0; background:rgba(0,0,0,0.6);"></div>

        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            style="position:relative; background:#111113; border:1px solid #2a2a35; border-radius:12px; padding:24px; width:100%; max-width:420px; z-index:10;"
        >
            <h3 style="color:#e2e2e9; font-size:15px; font-weight:600; margin:0 0 8px;">{{ $title }}</h3>
            <p style="color:#9b9bab; font-size:13px; line-height:1.6; margin:0 0 20px;">{{ $description }}</p>

            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" @click="open = false"
                    style="padding:7px 14px; background:transparent; border:1px solid #2a2a35; border-radius:7px; color:#9b9bab; font-size:13px; cursor:pointer; font-family:Inter,system-ui;">
                    {{ $cancel }}
                </button>
                @if($action)
                <form method="POST" action="{{ $action }}" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit"
                        style="padding:7px 14px; background:#7f1d1d20; border:1px solid #7f1d1d50; border-radius:7px; color:#f87171; font-size:13px; cursor:pointer; font-family:Inter,system-ui;">
                        {{ $confirm }}
                    </button>
                </form>
                @else
                <button type="button" @click="open = false; $dispatch('confirmed')"
                    style="padding:7px 14px; background:#7f1d1d20; border:1px solid #7f1d1d50; border-radius:7px; color:#f87171; font-size:13px; cursor:pointer; font-family:Inter,system-ui;">
                    {{ $confirm }}
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
