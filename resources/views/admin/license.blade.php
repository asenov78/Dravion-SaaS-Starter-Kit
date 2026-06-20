<x-layouts.admin :title="__('license.title')">

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('license.title') }}</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('license.subtitle') }}</p>
</div>

@if($errors->any())
<div class="mb-5 flex items-center gap-3 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ $errors->first() }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">

    {{-- Status card --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('license.status') }}</h3>
        </div>
        <div class="p-6">
            @php
                $key     = config('dravion.license_key', '');
                $warning = session('license_warning');
                $maskedKey = $key ? \App\Support\DomainHelper::maskKey($key) : null;
            @endphp

            @if(! $key)
                <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ __('license.no_key') }}
                </div>
            @elseif($warning)
                <div class="flex items-center gap-2 text-error-600 dark:text-error-400">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ __('license.invalid') }} — <span class="font-mono">{{ $maskedKey }}</span>
                </div>
            @else
                <div class="flex items-center gap-2 text-success-600 dark:text-success-400">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ __('license.licensed') }} — <span class="font-mono">{{ $maskedKey }}</span>
                </div>
            @endif
        </div>

        @if($key)
        <div class="px-6 pb-6">
            <form method="POST" action="{{ route('admin.license.remove') }}">
                @csrf @method('DELETE')
                <button type="submit" class="text-sm text-error-600 hover:underline dark:text-error-400">
                    {{ __('license.remove') }}
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Activate card --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-800">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('license.activate') }}</h3>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('admin.license.update') }}">
                @csrf @method('POST')
                <label for="license_key" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">
                    {{ __('license.enter_key') }}
                </label>
                <input id="license_key" name="license_key" type="text"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    placeholder="DRV-XXXXXXXXXXXXXXXXXXXXXXXX"
                    value="{{ old('license_key') }}" />
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('license.enter_key_desc') }}</p>
                <button type="submit"
                    class="mt-4 inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600">
                    {{ __('license.activate') }}
                </button>
            </form>
        </div>
    </div>

</div>

<div class="mt-4 text-xs text-gray-400 dark:text-gray-500">{{ __('license.info') }}</div>

</x-layouts.admin>
