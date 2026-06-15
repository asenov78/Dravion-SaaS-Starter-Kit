<x-layouts.admin :title="__('license.title')">

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('license.title') }}</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('license.subtitle') }}</p>
</div>


<div class="flex flex-col gap-5 max-w-2xl">

    {{-- Status card --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="text-brand-500" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('license.status') }}</h3>
                        @if($masked)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Key: <span class="font-mono text-brand-500">{{ $masked }}</span></p>
                        @else
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('license.no_key') }}</p>
                        @endif
                    </div>
                </div>
                @if($valid)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-success-500"></span>{{ __('license.licensed') }}
                </span>
                @elseif($masked)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-error-500"></span>{{ __('license.invalid') }}
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-warning-500"></span>{{ __('license.no_license') }}
                </span>
                @endif
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('admin.license.update') }}">
                @csrf
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('license.enter_key_desc') }}</p>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">{{ __('license.enter_key') }}</label>
                    <input type="text" name="license_key"
                        placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                        value="{{ old('license_key') }}"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-mono text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800
                        {{ $errors->has('license_key') ? 'border-error-400 dark:border-error-600' : '' }}" />
                    @error('license_key')
                    <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                        {{ __('license.activate') }}
                    </button>

                    @if($masked)
                    <button type="button"
                        onclick="if(confirm('Remove license key? This installation will become unlicensed.')) document.getElementById('remove-license-form').submit()"
                        class="inline-flex items-center rounded-lg border border-error-200 bg-error-50 px-5 py-2.5 text-sm font-medium text-error-700 hover:bg-error-100 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400 dark:hover:bg-error-500/20 transition-colors">
                        {{ __('license.remove') }}
                    </button>
                    @endif
                </div>
            </form>

            @if($masked)
            <form id="remove-license-form" method="POST" action="{{ route('admin.license.remove') }}" class="hidden">
                @csrf @method('DELETE')
            </form>
            @endif
        </div>
    </div>

    {{-- Info note --}}
    <div class="flex gap-3 rounded-xl border border-brand-100 bg-brand-50 px-5 py-4 dark:border-brand-500/20 dark:bg-brand-500/5">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand-500 mt-0.5 flex-shrink-0">
            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>
        </svg>
        <p class="text-sm text-brand-700 dark:text-brand-300 leading-relaxed">
            {{ __('license.info') }}
        </p>
    </div>

</div>

</x-layouts.admin>
