<x-layouts.admin :title="__('auth.2fa_manage_title')">
    <div style="max-width:480px; margin:0 auto;">
        <x-ui.card>
            <h2 style="font-size:16px; font-weight:600; margin:0 0 8px; color:var(--color-gray-900);" class="dark:text-white">
                {{ __('auth.2fa_manage_title') }}
            </h2>
            <p style="font-size:13px; color:var(--color-gray-500); margin:0 0 20px;">
                {{ __('auth.2fa_manage_description') }}
            </p>

            <div style="display:flex; align-items:center; gap:10px; padding:12px 14px; background:#ecfdf5; border:1px solid #6ee7b7; border-radius:10px; margin-bottom:20px;" class="dark:bg-green-900/20 dark:border-green-700">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                <span style="font-size:13px; font-weight:500; color:#065f46;" class="dark:text-green-300">{{ __('auth.2fa_enabled_badge') }}</span>
            </div>

            @if ($errors->any())
                <x-ui.alert variant="error" class="mb-4">{{ $errors->first() }}</x-ui.alert>
            @endif

            <form method="POST" action="{{ route('profile.two-factor.disable') }}">
                @csrf
                @method('DELETE')
                <div style="margin-bottom:16px;">
                    <x-ui.label for="password">{{ __('auth.current_password') }}</x-ui.label>
                    <x-ui.input id="password" name="password" type="password" autocomplete="current-password" />
                </div>
                <x-ui.button type="submit" variant="danger" style="width:100%;">{{ __('auth.2fa_disable') }}</x-ui.button>
            </form>
        </x-ui.card>
    </div>
</x-layouts.admin>
