<x-layouts.admin title="User Profile">
    <x-common.page-breadcrumb pageTitle="User Profile" />

    @if($errors->any())
        <x-ui.alert variant="error" :message="$errors->first()" class="mb-6" />
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Profile</h3>
        <x-profile.profile-card />
        <x-profile.personal-info-card />
        <x-profile.address-card />
    </div>

    {{-- Language preference --}}
    @php $languages = \App\Models\Language::orderBy('name')->get(); @endphp
    @if($languages->count() > 1)
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Language</h3>
        <form method="POST" action="{{ route('profile.locale') }}" class="flex items-center gap-3">
            @csrf @method('PATCH')
            <select name="locale"
                class="rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                @foreach($languages as $lang)
                <option value="{{ $lang->code }}" {{ auth()->user()->locale === $lang->code ? 'selected' : '' }}>
                    {{ $lang->flag }} {{ $lang->name }}
                </option>
                @endforeach
            </select>
            <button type="submit"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                Save
            </button>
        </form>
    </div>
    @endif

    {{-- Two-Factor Authentication --}}
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90" style="margin:0 0 4px;">{{ __('auth.2fa_manage_title') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400" style="margin:0;">
                    @if(auth()->user()->two_factor_confirmed_at)
                        {{ __('auth.2fa_enabled_badge') }}
                    @else
                        {{ __('auth.2fa_setup_title') }}
                    @endif
                </p>
            </div>
            <a href="{{ route('profile.two-factor') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-300 dark:hover:bg-white/[0.06] transition-colors">
                @if(auth()->user()->two_factor_confirmed_at)
                    {{ __('app.edit') }}
                @else
                    {{ __('auth.2fa_enable') }}
                @endif
            </a>
        </div>
    </div>

    {{-- Change password --}}
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Change Password</h3>
        <form method="POST" action="{{ route('profile.password') }}" class="max-w-md flex flex-col gap-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">Current Password</label>
                <input type="password" name="current_password"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" required />
                @error('current_password')
                    <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">New Password</label>
                <input type="password" name="password"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" required />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">Confirm New Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90" required />
            </div>
            <div>
                <button type="submit"
                    class="inline-flex items-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
