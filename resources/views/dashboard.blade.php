<x-layouts.portal :title="\App\Models\Setting::get('app_name', config('app.name'))">

@php
    $appName    = \App\Models\Setting::get('app_name', config('app.name'));
    $version    = config('dravion.version');
    $licenseKey = config('dravion.license_key');
    $licensed   = !empty($licenseKey) && !str_starts_with($licenseKey, 'DEV-');
    $devLicense = !empty($licenseKey) && str_starts_with($licenseKey, 'DEV-');
    $env        = config('app.env');
    $domain     = parse_url(config('app.url'), PHP_URL_HOST);

    if ($licensed)       { $licenseLabel = 'Licensed';    $licenseClass = 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400'; $dotClass = 'bg-success-500'; }
    elseif ($devLicense) { $licenseLabel = 'Dev License'; $licenseClass = 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400'; $dotClass = 'bg-warning-500'; }
    else                 { $licenseLabel = 'Unlicensed';  $licenseClass = 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400';   $dotClass = 'bg-error-500'; }
@endphp

<div class="flex items-center justify-center min-h-[calc(100vh-60px)] px-4 py-10">
<div class="w-full max-w-lg">

    {{-- Card --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- Header --}}
        <div class="flex items-center gap-4 px-6 py-5 border-b border-gray-100 dark:border-gray-800">
            <span class="flex items-center justify-center w-10 h-10 rounded-xl bg-brand-500 flex-shrink-0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="white"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </span>
            <div>
                <h1 class="text-base font-bold tracking-widest text-gray-800 dark:text-white/90">{{ strtoupper($appName) }}</h1>
                <p class="text-xs text-gray-400 dark:text-gray-500 tracking-widest mt-0.5">
                    @auth WELCOME, {{ strtoupper(auth()->user()->name) }}
                    @else SAAS STARTER KIT
                    @endauth
                </p>
            </div>
            <span class="ml-auto inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $licenseClass }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>{{ $licenseLabel }}
            </span>
        </div>

        {{-- Stats row --}}
        <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100 dark:divide-gray-800 dark:border-gray-800">
            <div class="px-5 py-4">
                <p class="text-[10px] font-semibold tracking-widest text-gray-400 dark:text-gray-500 uppercase mb-1.5">Version</p>
                <p class="text-lg font-bold text-brand-500 tracking-tight">v{{ $version }}</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-[10px] font-semibold tracking-widest text-gray-400 dark:text-gray-500 uppercase mb-1.5">Environment</p>
                <p class="text-lg font-bold tracking-tight {{ $env === 'production' ? 'text-success-500' : 'text-warning-500' }}">
                    {{ strtoupper($env) }}
                </p>
            </div>
            <div class="px-5 py-4">
                <p class="text-[10px] font-semibold tracking-widest text-gray-400 dark:text-gray-500 uppercase mb-1.5">Domain</p>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">{{ $domain }}</p>
            </div>
        </div>

        {{-- Info rows --}}
        <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
            @auth
            <div class="flex items-center justify-between px-6 py-3">
                <span class="text-xs tracking-widest text-gray-400 dark:text-gray-500 uppercase">Role</span>
                <span class="text-xs font-semibold tracking-widest text-gray-700 dark:text-gray-300 uppercase">{{ auth()->user()->roles->first()?->name ?? 'user' }}</span>
            </div>
            @endauth
            <div class="flex items-center justify-between px-6 py-3">
                <span class="text-xs tracking-widest text-gray-400 dark:text-gray-500 uppercase">Platform</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">Dravion SaaS Starter Kit</span>
            </div>
        </div>

        {{-- Footer --}}
        @if(auth()->user()?->hasAnyRole(['admin','manager']))
        <div class="flex justify-end px-6 py-4 border-t border-gray-100 dark:border-gray-800">
            <a href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-transparent dark:text-gray-300 dark:hover:bg-gray-800 transition-colors tracking-widest">
                ADMIN PANEL →
            </a>
        </div>
        @endif
    </div>

    <p class="text-center text-[10px] tracking-widest text-gray-300 dark:text-gray-600 uppercase mt-6">
        {{ $appName }} · v{{ $version }}
    </p>

</div>
</div>

</x-layouts.portal>
