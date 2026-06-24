<x-layouts.admin :title="__('users.edit')">

@php $u = $user; @endphp

<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <div>
        <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
            <a href="{{ route('admin.users.index') }}" class="hover:text-brand-500">{{ __('users.title') }}</a>
            <span>/</span>
            <span class="text-gray-800 dark:text-white/90">{{ $u->name }}</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('users.edit') }}</h2>
    </div>
    <a href="{{ route('admin.users.index') }}"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        {{ __('app.back') }}
    </a>
</div>

@if($errors->any())
    <x-ui.alert variant="error" :message="$errors->first()" class="mb-6" />
@endif

<form method="POST" action="{{ route('admin.users.update', $u) }}" enctype="multipart/form-data" class="flex flex-col gap-6">
    @csrf @method('PUT')

    {{-- Avatar --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('users.profile_photo') }}</h3>
        </div>
        <div class="p-6 flex flex-wrap items-center gap-6">
            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-full bg-brand-500 flex items-center justify-center text-2xl font-semibold text-white">
                @if($u->avatar)
                    <img src="{{ url('storage/' . $u->avatar) }}" alt="{{ $u->name }}" class="h-full w-full object-cover">
                @else
                    {{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}
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
                <input type="text" name="name" value="{{ old('name', $u->name) }}" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800 {{ $errors->has('name') ? 'border-error-400' : '' }}" />
                @error('name') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('app.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $u->email) }}" required
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800 {{ $errors->has('email') ? 'border-error-400' : '' }}" />
                @error('email') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('users.new_password') }}</label>
                <input type="password" name="password" placeholder="{{ __('users.password_hint') }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
                @error('password') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('app.role') }}</label>
                <select name="role"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800">
                    @foreach($roles->pluck('name','name') as $val => $label)
                    <option value="{{ $val }}" {{ old('role', $u->getRoleNames()->first()) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('role') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Dynamic custom categories (Personal Info, Address, + any custom ones) --}}
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
                // system fields map to user columns
                $systemColumnMap = ['phone' => $u->phone, 'country' => $u->country, 'city_state' => $u->city_state];
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
                        <option value="">—</option>
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
                                class="w-4 h-4 rounded border border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700">
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
                                class="w-4 h-4 rounded border border-gray-300 text-brand-500 focus:ring-brand-500/20 dark:border-gray-700">
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

    <div class="flex flex-wrap items-center justify-between gap-3">
        @role('admin')
        @if($u->id !== auth()->id())
        <div class="flex items-center gap-2">
            @if($u->two_factor_confirmed_at)
            <button type="button" x-data
                @click="$dispatch('confirm-action', { title: '{{ addslashes(__('users.reset_2fa_title', ['name' => $u->name])) }}', message: '{{ addslashes(__('users.reset_2fa_confirm')) }}', btnLabel: '{{ addslashes(__('users.reset_2fa')) }}', btnColor: '#d97706', url: '{{ route('admin.users.two-factor.reset', $u) }}', method: 'DELETE', successAction: 'redirect', targetId: '{{ route('admin.users.edit', $u) }}', toastMessage: '{{ addslashes(__('flash.two_factor_reset', ['name' => $u->name])) }}', toastVariant: 'success' })"
                class="inline-flex items-center rounded-lg border border-warning-300 bg-warning-50 px-4 py-2.5 text-sm font-medium text-warning-700 hover:bg-warning-100 dark:border-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
                {{ __('users.reset_2fa') }}
            </button>
            @endif
            <button type="button" x-data
                @click="$dispatch('confirm-action', { title: '{{ addslashes(__('app.delete')) }} {{ addslashes($u->name) }}?', message: '{{ addslashes(__('users.confirm_delete', ['name' => $u->name])) }}', btnLabel: '{{ addslashes(__('app.delete')) }}', btnColor: '#dc2626', url: '{{ route('admin.users.destroy', $u) }}', method: 'DELETE', successAction: 'redirect', targetId: '{{ route('admin.users.index') }}', toastMessage: '{{ addslashes(__('flash.user_deleted')) }}', toastVariant: 'error' })"
                class="inline-flex items-center rounded-lg border border-error-300 bg-error-50 px-4 py-2.5 text-sm font-medium text-error-700 hover:bg-error-100 dark:border-error-700 dark:bg-error-500/10 dark:text-error-400">
                {{ __('app.delete') }}
            </button>
        </div>
        @else
        <div></div>
        @endif
        @else
        <div></div>
        @endrole

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800">
                {{ __('app.cancel') }}
            </a>
            <button type="submit"
                class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                {{ __('users.save_changes') }}
            </button>
        </div>
    </div>
</form>

</x-layouts.admin>
