<x-layouts.admin :title="__('nav.profile')">
    <x-common.page-breadcrumb :pageTitle="__('nav.profile')" />

    @if($errors->any())
        <x-ui.alert variant="error" :message="$errors->first()" class="mb-6" />
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 lg:p-6">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">{{ __('nav.profile') }}</h3>
        <x-profile.profile-card />
        <x-profile.personal-info-card />
        <x-profile.address-card />
    </div>

    {{-- Language preference --}}
    @php $languages = \App\Models\Language::orderBy('name')->get(); @endphp
    @if($languages->count() > 1)
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 lg:p-6">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('nav.language_pref') }}</h3>
        <form method="POST" action="{{ route('profile.locale') }}" class="flex items-center gap-3">
            @csrf @method('PATCH')
            <select name="locale"
                class="rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-600 dark:bg-gray-700 dark:text-white/90">
                @foreach($languages as $lang)
                <option value="{{ $lang->code }}" {{ auth()->user()->locale === $lang->code ? 'selected' : '' }}>
                    {{ $lang->flag }} {{ $lang->name }}
                </option>
                @endforeach
            </select>
            <button type="submit"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                {{ __('app.save') }}
            </button>
        </form>
    </div>
    @endif

    {{-- Two-Factor Authentication --}}
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 lg:p-6">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('auth.2fa_manage_title') }}</h3>

        @if(auth()->user()->two_factor_confirmed_at)
            {{-- ENABLED --}}
            <div class="mb-5 flex items-center gap-2.5 rounded-xl px-4 py-3"
                :style="$store.theme.theme === 'dark'
                    ? 'background-color:#14532d;border:1px solid #166534;'
                    : 'background-color:#f0fdf4;border:1px solid #bbf7d0;'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    :style="$store.theme.theme === 'dark' ? 'color:#4ade80;flex-shrink:0;' : 'color:#16a34a;flex-shrink:0;'"><polyline points="20 6 9 17 4 12"/></svg>
                <span class="text-sm font-medium"
                    :style="$store.theme.theme === 'dark' ? 'color:#86efac;' : 'color:#15803d;'">{{ __('auth.2fa_enabled_badge') }}</span>
            </div>
            <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">{{ __('auth.2fa_manage_description') }}</p>

            @if($errors->has('password'))
                <x-ui.alert variant="error" class="mb-4">{{ $errors->first('password') }}</x-ui.alert>
            @endif

            <form method="POST" action="{{ route('profile.two-factor.disable') }}" class="max-w-sm">
                @csrf @method('DELETE')
                <div style="margin-bottom:14px;">
                    <x-ui.label for="2fa_password">{{ __('auth.current_password') }}</x-ui.label>
                    <x-ui.input id="2fa_password" name="password" type="password" autocomplete="current-password" />
                </div>
                <x-ui.button type="submit" variant="danger">{{ __('auth.2fa_disable') }}</x-ui.button>
            </form>

        @else
            {{-- NOT ENABLED --}}
            <p class="mb-5 text-sm text-gray-500 dark:text-gray-400">{{ __('auth.2fa_setup_instructions') }}</p>

            <div class="mb-5 flex justify-center rounded-xl bg-gray-50 p-4 dark:bg-gray-700">
                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($qrUrl) }}&size=180x180"
                    alt="QR Code" style="width:180px;height:180px;border-radius:8px;">
            </div>

            @if($errors->has('code'))
                <x-ui.alert variant="error" class="mb-4">{{ $errors->first('code') }}</x-ui.alert>
            @endif

            <form method="POST" action="{{ route('profile.two-factor.confirm') }}" class="max-w-sm">
                @csrf
                <div style="margin-bottom:14px;">
                    <x-ui.label for="2fa_code">{{ __('auth.2fa_enter_code') }}</x-ui.label>
                    <x-ui.input id="2fa_code" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}"
                        maxlength="6" autocomplete="one-time-code"
                        placeholder="000000" style="text-align:center;letter-spacing:0.3em;font-size:18px;" />
                </div>
                <x-ui.button type="submit">{{ __('auth.2fa_enable') }}</x-ui.button>
            </form>
        @endif
    </div>

    {{-- Change password --}}
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 lg:p-6">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('nav.change_password') }}</h3>
        <form method="POST" action="{{ route('profile.password') }}" class="max-w-md flex flex-col gap-5">
            @csrf @method('PUT')
            <div>
                <x-ui.label for="current_password">{{ __('auth.current_password') }}</x-ui.label>
                <x-ui.input id="current_password" name="current_password" type="password" autocomplete="current-password" />
                @error('current_password')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <x-ui.label for="password">{{ __('auth.new_password') }}</x-ui.label>
                <x-ui.input id="password" name="password" type="password" autocomplete="new-password" />
            </div>
            <div>
                <x-ui.label for="password_confirmation">{{ __('auth.confirm_password') }}</x-ui.label>
                <x-ui.input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
            </div>
            <div>
                <x-ui.button type="submit">{{ __('auth.update_password') }}</x-ui.button>
            </div>
        </form>
    </div>
</x-layouts.admin>
