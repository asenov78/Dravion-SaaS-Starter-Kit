<x-layouts.auth :title="__('auth.2fa_title') . ' — ' . config('app.name')">
<div class="relative z-1 bg-white dark:bg-gray-900">
    <div class="relative flex h-screen w-full flex-col justify-center lg:flex-row dark:bg-gray-900">

        {{-- Left: Form --}}
        <div class="flex w-full flex-col justify-center lg:w-1/2">
            <div class="mx-auto w-full max-w-md px-6 py-10 lg:px-0">
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-1 text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 mb-8">
                    <svg class="stroke-current" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M12.7083 5L7.5 10.2083L12.7083 15.4167" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ __('auth.back_to_login') }}
                </a>
                <div class="mb-5 sm:mb-8">
                    <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-gray-800 dark:text-white/90">
                        {{ __('auth.2fa_title') }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('auth.2fa_subtitle') }}
                    </p>
                </div>

                @if($errors->any())
                    <div class="mb-4 rounded-lg border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-400">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('two-factor.verify') }}">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                {{ __('auth.2fa_code') }}<span class="text-error-500">*</span>
                            </label>
                            <input type="text" name="code" inputmode="numeric" pattern="[0-9]{6}"
                                maxlength="6" autocomplete="one-time-code" autofocus
                                placeholder="000000" required
                                class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-center text-lg tracking-[0.3em] text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        </div>
                        @if(($rememberDays ?? 0) > 0)
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="remember_device" value="1"
                                class="w-5 h-5 rounded border border-gray-300 bg-white text-brand-500 focus:ring-brand-500/20 dark:border-gray-700 dark:bg-gray-900 dark:checked:bg-brand-500" />
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('auth.2fa_remember_device', ['days' => $rememberDays]) }}</span>
                        </label>
                        @endif

                        <button type="submit"
                            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                            {{ __('auth.2fa_verify') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>

        {{-- Right: Brand panel --}}
        <div class="bg-brand-950 relative hidden h-full w-full items-center lg:grid lg:w-1/2 dark:bg-white/5">
            <div class="z-1 flex items-center justify-center">
                <x-common.common-grid-shape />
                <div class="flex max-w-xs flex-col items-center text-center">
                    <a href="{{ url('/') }}" class="mb-4 block">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand-500">
                            <span class="text-white font-bold text-2xl">{{ strtoupper(substr(config('app.name'), 0, 1)) }}</span>
                        </div>
                    </a>
                    <h2 class="text-xl font-semibold text-white mb-2">{{ config('app.name') }}</h2>
                    <p class="text-gray-400 dark:text-white/60 text-sm">{{ __('auth.brand_tagline') }}</p>
                </div>
            </div>
        </div>


    </div>
</div>
</x-layouts.auth>
