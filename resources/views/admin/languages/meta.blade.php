<x-layouts.admin :title="__('languages.edit_title')">

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('languages.edit_title') }}</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('languages.edit_subtitle') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.languages.edit', $language) }}"
           class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
            {{ __('languages.edit_translations') }}
        </a>
        <a href="{{ route('admin.languages.index') }}"
           class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
            ← {{ __('app.back') }}
        </a>
    </div>
</div>

@if($errors->any())
    <x-ui.alert variant="error" :message="$errors->first()" class="mb-6" />
@endif

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">
            {{ $language->flag }} {{ $language->name }}
            <span class="ml-2 text-sm font-normal text-gray-400">({{ $language->code }})</span>
            @if($language->is_default)
                <span class="ml-2 rounded-full bg-brand-100 px-2 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-900/30 dark:text-brand-300">{{ __('languages.default') }}</span>
            @endif
        </h3>
    </div>
    <div class="p-6 max-w-lg">
        <form method="POST" action="{{ route('admin.languages.meta.update', $language) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ __('languages.code_label') }}
                </label>
                <input type="text" value="{{ $language->code }}" disabled
                    class="h-11 w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500 cursor-not-allowed" />
                <p class="mt-1 text-xs text-gray-400">{{ __('languages.code_readonly') }}</p>
            </div>

            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ __('languages.name_label') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" maxlength="100" required
                    value="{{ old('name', $language->name) }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('name') border-red-400 @enderror" />
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="flag" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ __('languages.flag_label') }}
                </label>
                <input type="text" id="flag" name="flag" maxlength="10"
                    value="{{ old('flag', $language->flag) }}"
                    placeholder="e.g. 🇬🇧"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit"
                    class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/50">
                    {{ __('languages.save') }}
                </button>
                <a href="{{ route('admin.languages.index') }}"
                    class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    {{ __('app.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

</x-layouts.admin>
