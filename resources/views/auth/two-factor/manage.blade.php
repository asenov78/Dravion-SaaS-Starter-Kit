<x-layouts.admin :title="__('auth.2fa_manage_title')">
    <div style="max-width:480px; margin:0 auto;">
        <x-ui.card>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white" style="margin:0 0 8px;">
                {{ __('auth.2fa_manage_title') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400" style="margin:0 0 20px;">
                {{ __('auth.2fa_manage_description') }}
            </p>

            <div class="bg-success-50 dark:bg-success-500/10 border border-success-200 dark:border-success-700" style="display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:10px; margin-bottom:20px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-success-600 dark:text-success-400"><polyline points="20 6 9 17 4 12"/></svg>
                <span class="text-sm font-medium text-success-700 dark:text-success-300">{{ __('auth.2fa_enabled_badge') }}</span>
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
