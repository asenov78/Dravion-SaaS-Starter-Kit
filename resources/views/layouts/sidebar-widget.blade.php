<div class="mx-auto mb-6 w-full max-w-60 rounded-2xl bg-gray-50 px-4 py-5 text-center dark:bg-white/[0.03]">
    <h3 class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">
        Dravion Admin
    </h3>
    <p class="mb-3 text-gray-500 text-theme-xs dark:text-gray-400">
        SaaS Starter Kit v{{ config('dravion.version', '1.0') }}
    </p>
    <a href="{{ route('dashboard') }}"
        class="flex items-center justify-center p-2.5 font-medium text-white rounded-lg bg-brand-500 text-theme-sm hover:bg-brand-600">
        ← User Portal
    </a>
</div>
