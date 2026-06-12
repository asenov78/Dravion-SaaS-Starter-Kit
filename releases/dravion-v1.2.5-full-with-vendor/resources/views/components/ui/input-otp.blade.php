@props([
    'name'   => 'otp',
    'digits' => 6,
    'label'  => null,
])

<div style="font-family:Inter,system-ui;" x-data="otpInput('{{ $name }}', {{ $digits }})" {{ $attributes }}>
    @if($label)
    <label style="display:block; color:#6b6b7b; font-size:12px; font-weight:500; margin-bottom:10px;">{{ $label }}</label>
    @endif

    <div style="display:flex; gap:8px; align-items:center;">
        @for($i = 0; $i < $digits; $i++)
        <input
            type="text"
            maxlength="1"
            inputmode="numeric"
            pattern="[0-9]*"
            name="{{ $name }}"
            x-ref="digit{{ $i }}"
            @input="onInput($event, {{ $i }})"
            @keydown.backspace="onBackspace($event, {{ $i }})"
            @paste.prevent="onPaste($event)"
            style="width:40px; height:44px; text-align:center; background:#0a0a0b; border:1px solid #2a2a35; border-radius:7px; color:#e2e2e9; font-size:18px; font-weight:600; outline:none; font-family:ui-monospace,monospace;"
            onfocus="this.style.borderColor='#5e6ad2'"
            onblur="this.style.borderColor='#2a2a35'"
        >
        @if($i === intdiv($digits, 2) - 1 && $digits % 2 === 0)
        <span style="color:#3a3a45; font-size:20px;">—</span>
        @endif
        @endfor
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    if (Alpine.data && !window._otpInputRegistered) {
        window._otpInputRegistered = true;
        Alpine.data('otpInput', (name, digits) => ({
            onInput(e, idx) {
                const v = e.target.value.replace(/\D/g, '');
                e.target.value = v.slice(-1);
                if (v && idx < digits - 1) this.$refs['digit' + (idx + 1)].focus();
            },
            onBackspace(e, idx) {
                if (!e.target.value && idx > 0) this.$refs['digit' + (idx - 1)].focus();
            },
            onPaste(e) {
                const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
                text.split('').slice(0, digits).forEach((ch, i) => {
                    if (this.$refs['digit' + i]) this.$refs['digit' + i].value = ch;
                });
                const last = Math.min(text.length, digits) - 1;
                if (this.$refs['digit' + last]) this.$refs['digit' + last].focus();
            },
        }));
    }
});
</script>
