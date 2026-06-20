<x-layouts.portal :title="__('app.dashboard')">

<div class="px-6 py-8 max-w-4xl mx-auto">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">
            {{ __('app.welcome') }}, {{ auth()->user()->name }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ config('app.name') }} &mdash; v{{ config('dravion.version') }}
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-2">{{ __('app.account') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-2">{{ __('app.quick_links') }}</h2>
            <ul class="space-y-1 text-sm">
                <li>
                    <a href="{{ route('api-tokens.index') }}" class="text-brand-500 hover:underline">{{ __('tokens.title') }}</a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:underline dark:text-gray-400">
                            {{ __('auth.logout') }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>

</div>

</x-layouts.portal>
