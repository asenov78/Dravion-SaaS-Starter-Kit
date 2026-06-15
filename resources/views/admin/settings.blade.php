<x-layouts.admin :title="__('settings.title')">

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('settings.title') }}</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('settings.subtitle') }}</p>
</div>

@if($errors->any())
<div class="mb-5 flex items-center gap-3 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="flex flex-col gap-5 max-w-2xl">

    {{-- General --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('settings.general') }}</h3>
        </div>
        <div class="p-6 flex flex-col gap-5">
            <div>
                <label for="app_name" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('settings.app_name') }}</label>
                <input type="text" name="app_name" id="app_name" value="{{ $settings['app_name'] }}"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" required />
            </div>
            <div>
                <label for="app_url" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('settings.app_url') }}</label>
                <input type="url" name="app_url" id="app_url" value="{{ $settings['app_url'] }}"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" required />
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('settings.registration') }}</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('settings.registration_desc') }}</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" name="registration" value="1" class="sr-only peer"
                        {{ ($settings['registration'] ?? '0') === '1' ? 'checked' : '' }}>
                    <div class="h-6 w-11 rounded-full border border-gray-200 bg-gray-100 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:border-brand-500 dark:border-gray-700 dark:bg-gray-700"></div>
                </label>
            </div>
        </div>
    </div>

    {{-- Logo --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('settings.logo') }}</h3>
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('settings.logo_desc') }}</p>
        </div>
        <div class="p-6 flex items-center gap-6">
            <div class="h-14 w-14 shrink-0 flex items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 overflow-hidden">
                @if($settings['logo'])
                    <img src="{{ Storage::url($settings['logo']) }}" alt="Logo" class="h-full w-full object-contain p-1">
                @else
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-gray-400"><rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1.5"/><path d="M3 9l4-4 4 4 4-4 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                @endif
            </div>
            <div>
                <input type="file" name="logo" accept="image/*"
                    class="block text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100 dark:text-gray-400 dark:file:bg-brand-500/10 dark:file:text-brand-400">
                @error('logo') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- System --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('nav.system') }}</h3>
        </div>
        <div class="p-6 flex flex-col gap-5">
            <div>
                <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('settings.timezone') }}</label>
                <select name="timezone" id="timezone"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @foreach($timezones as $tz)
                    <option value="{{ $tz }}" {{ $settings['timezone'] === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_format" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('settings.date_format') }}</label>
                <input type="text" name="date_format" id="date_format" value="{{ $settings['date_format'] }}"
                    placeholder="d/m/Y"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('settings.date_format_desc') }}</p>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('settings.maintenance') }}</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('settings.maintenance_desc') }}</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" name="maintenance" value="1" class="sr-only peer"
                        {{ ($settings['maintenance'] ?? '0') === '1' ? 'checked' : '' }}>
                    <div class="h-6 w-11 rounded-full border border-gray-200 bg-gray-100 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:border-brand-500 dark:border-gray-700 dark:bg-gray-700"></div>
                </label>
            </div>
        </div>
    </div>

    {{-- Email --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('settings.email') }}</h3>
        </div>
        <div class="p-6 flex flex-col gap-5">
            <div>
                <label for="mail_from" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('settings.from_address') }}</label>
                <input type="email" name="mail_from" id="mail_from" value="{{ $settings['mail_from'] }}"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
            </div>
            <div>
                <label for="mail_from_name" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('settings.from_name') }}</label>
                <input type="text" name="mail_from_name" id="mail_from_name" value="{{ $settings['mail_from_name'] }}"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" />
            </div>
            {{-- Welcome email toggle --}}
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('settings.mail_welcome') }}</label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('settings.mail_welcome_desc') }}</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" name="mail_welcome" value="1" class="sr-only peer"
                        {{ ($settings['mail_welcome'] ?? '1') === '1' ? 'checked' : '' }}>
                    <div class="h-6 w-11 rounded-full border border-gray-200 bg-gray-100 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:border-brand-500 dark:border-gray-700 dark:bg-gray-700"></div>
                </label>
            </div>

            {{-- SMTP test --}}
            <div x-data="{ loading: false, result: null, ok: null }" class="flex items-center gap-4 pt-1">
                <button type="button"
                    @click="loading = true; result = null;
                        fetch('{{ route('admin.settings.smtp-test') }}', {
                            method: 'POST',
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
                        })
                        .then(r => r.json())
                        .then(d => { ok = d.ok; result = d.message; loading = false; })
                        .catch(e => { ok = false; result = e.message; loading = false; })"
                    :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800 disabled:opacity-60 transition-colors">
                    <svg x-show="!loading" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    <svg x-show="loading" class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    {{ __('settings.smtp_test') }}
                </button>
                <p x-show="result !== null" x-text="result"
                    :class="ok ? 'text-success-600 dark:text-success-400' : 'text-error-600 dark:text-error-400'"
                    class="text-sm" x-cloak></p>
            </div>
        </div>
    </div>

    {{-- License info --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('nav.license') }}</h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            <div class="flex items-center justify-between px-6 py-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('settings.license_key') }}</span>
                @if(config('dravion.license_key'))
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>{{ __('app.active') }}
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-error-500"></span>{{ __('settings.not_set') }}
                </span>
                @endif
            </div>
            <div class="flex items-center justify-between px-6 py-3">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('settings.version') }}</span>
                <span class="text-sm font-mono text-gray-700 dark:text-gray-300">v{{ config('dravion.version') }}</span>
            </div>
        </div>
    </div>

    {{-- Activity Log --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('settings.activity_log') }}</h3>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('settings.activity_log_desc') }}</p>
        </div>
        <div class="p-6 flex flex-col gap-4">
            @foreach([
                'activity_log_auth'     => [__('settings.log_auth'),     __('settings.log_auth_desc')],
                'activity_log_users'    => [__('settings.log_users'),    __('settings.log_users_desc')],
                'activity_log_profile'  => [__('settings.log_profile'),  __('settings.log_profile_desc')],
                'activity_log_settings' => [__('settings.log_settings'), __('settings.log_settings_desc')],
            ] as $key => [$label, $desc])
            <label class="flex items-start gap-4 cursor-pointer group">
                <div class="relative mt-0.5">
                    <input type="checkbox" name="{{ $key }}" value="1" class="sr-only peer"
                        {{ ($settings[$key] ?? '1') === '1' ? 'checked' : '' }}>
                    <div class="h-6 w-11 rounded-full border border-gray-200 bg-gray-100 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-brand-500 peer-checked:after:translate-x-full peer-checked:after:border-white dark:border-gray-700 dark:bg-gray-700"></div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                </div>
            </label>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit"
            class="inline-flex items-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            {{ __('settings.save') }}
        </button>
    </div>

</div>
</form>

</x-layouts.admin>

