<x-layouts.admin :title="__('nav.profile')">
<x-common.page-breadcrumb :pageTitle="__('nav.profile')" />

@if($errors->any())
    <x-ui.alert variant="error" :message="$errors->first()" class="mb-6" />
@endif

{{-- Profile + Custom Fields form --}}
<form method="POST" action="{{ route('admin.ui.profile.update') }}" enctype="multipart/form-data" class="flex flex-col gap-6">
    @csrf @method('PUT')

    {{-- Avatar --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('users.profile_photo') }}</h3>
        </div>
        <div class="p-6 flex flex-wrap items-center gap-6">
            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-full bg-brand-500 flex items-center justify-center text-2xl font-semibold text-white">
                @if($user->avatar)
                    <img src="{{ url('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                @else
                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                @endif
            </div>
            <div>
                <input type="file" name="avatar" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100 dark:text-gray-400 dark:file:bg-brand-500/10 dark:file:text-brand-400">
                <p class="mt-1 text-xs text-gray-400">{{ __('users.avatar_hint') }}</p>
                @error('avatar') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Account --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('users.account') }}</h3>
        </div>
        <div class="p-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('auth.full_name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                @error('name') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('auth.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />
                @error('email') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Custom categories --}}
    @foreach($customCategories as $category)
    @if($category->fields->isNotEmpty())
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ $category->label() }}</h3>
        </div>
        <div class="p-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
            @foreach($category->fields as $field)
            @php
                $inputName = "field_{$field->id}";
                $value = old($inputName, $fieldValues[$field->id] ?? '');
                $systemColumnMap = ['phone' => $user->phone, 'country' => $user->country, 'city_state' => $user->city_state];
                if ($field->is_system && array_key_exists($field->key, $systemColumnMap)) {
                    $inputName = $field->key;
                    $value = old($field->key, $systemColumnMap[$field->key] ?? '');
                }
            @endphp
            <div @if($field->type === 'textarea') class="lg:col-span-2" @endif>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">
                    {{ $field->label() }}@if($field->is_required)<span class="text-error-500 ml-0.5">*</span>@endif
                </label>

                @if($field->type === 'text')
                    <input type="text" name="{{ $inputName }}" value="{{ $value }}"
                        @if($field->is_required) required @endif
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" />

                @elseif($field->type === 'textarea')
                    <textarea name="{{ $inputName }}" rows="3"
                        @if($field->is_required) required @endif
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">{{ $value }}</textarea>

                @elseif($field->type === 'select')
                    <select name="{{ $inputName }}" @if($field->is_required) required @endif
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                        <option value="">&#8212;</option>
                        @foreach(($field->options ?? []) as $opt)
                        @php $optVal = is_array($opt) ? $field->optionValue($opt) : $opt; @endphp
                        <option value="{{ $optVal }}" {{ $value === $optVal ? 'selected' : '' }}>
                            {{ is_array($opt) ? $field->optionLabel($opt) : $opt }}
                        </option>
                        @endforeach
                    </select>

                @elseif($field->type === 'checkbox')
                    @php $checkedValues = array_filter(explode(',', (string)$value)); @endphp
                    @if($field->options)
                        <div class="flex flex-wrap gap-4 mt-2">
                        @foreach($field->options as $opt)
                        @php $optVal = is_array($opt) ? $field->optionValue($opt) : $opt; @endphp
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="{{ $inputName }}[]" value="{{ $optVal }}"
                                {{ in_array($optVal, $checkedValues) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border border-gray-300 text-brand-500 dark:border-gray-700">
                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                {{ is_array($opt) ? $field->optionLabel($opt) : $opt }}
                            </span>
                        </label>
                        @endforeach
                        </div>
                    @else
                        <label class="flex items-center gap-2 mt-2 cursor-pointer">
                            <input type="hidden" name="{{ $inputName }}" value="0">
                            <input type="checkbox" name="{{ $inputName }}" value="1" {{ $value ? 'checked' : '' }}
                                class="w-4 h-4 rounded border border-gray-300 text-brand-500 dark:border-gray-700">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $field->label() }}</span>
                        </label>
                    @endif
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach

    <div class="flex justify-end">
        <button type="submit"
            class="inline-flex items-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            {{ __('users.save_changes') }}
        </button>
    </div>
</form>

{{-- Language preference --}}
@php $languages = \App\Models\Language::orderBy('name')->get(); @endphp
@if($languages->count() > 1)
<div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 lg:p-6">
    <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('nav.language_pref') }}</h3>
    <form method="POST" action="{{ route('profile.locale') }}" class="flex flex-wrap items-center gap-3">
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
            {{ __('app.save') }}
        </button>
    </form>
</div>
@endif

{{-- Two-Factor Authentication --}}
<div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 lg:p-6">
    <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">{{ __('auth.2fa_manage_title') }}</h3>

    @if(auth()->user()->two_factor_confirmed_at)
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
        <p class="mb-5 text-sm text-gray-500 dark:text-gray-400">{{ __('auth.2fa_setup_instructions') }}</p>
        <div class="mb-5 flex justify-center rounded-xl bg-gray-50 p-4 dark:bg-gray-800">
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
<div class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 lg:p-6">
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