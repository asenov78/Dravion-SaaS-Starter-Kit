<x-layouts.admin :title="__('auth.2fa_setup_title')">
    <div style="max-width:480px; margin:0 auto;">
        <x-ui.card>
            <h2 style="font-size:16px; font-weight:600; margin:0 0 8px; color:var(--color-gray-900);" class="dark:text-white">
                {{ __('auth.2fa_setup_title') }}
            </h2>
            <p style="font-size:13px; color:var(--color-gray-500); margin:0 0 20px;">
                {{ __('auth.2fa_setup_instructions') }}
            </p>

            <div style="display:flex; justify-content:center; margin-bottom:20px; padding:16px; background:#f9fafb; border-radius:12px;" class="dark:bg-gray-800">
                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrUrl) }}&size=180x180" alt="QR Code" style="width:180px; height:180px; border-radius:8px;">
            </div>

            @if ($errors->any())
                <x-ui.alert variant="error" class="mb-4">{{ $errors->first() }}</x-ui.alert>
            @endif

            <form method="POST" action="{{ route('profile.two-factor.confirm') }}">
                @csrf
                <div style="margin-bottom:16px;">
                    <x-ui.label for="code">{{ __('auth.2fa_enter_code') }}</x-ui.label>
                    <x-ui.input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}"
                        maxlength="6" autocomplete="one-time-code" autofocus
                        placeholder="000000" style="text-align:center; letter-spacing:0.3em; font-size:18px;" />
                </div>
                <x-ui.button type="submit" style="width:100%;">{{ __('auth.2fa_enable') }}</x-ui.button>
            </form>
        </x-ui.card>
    </div>
</x-layouts.admin>
